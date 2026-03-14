<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Payment;
use App\Models\Student;
use App\Models\TermPercentage;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classe')->orderBy('fullname')->get();
        $classes = Classe::orderBy('name')->get();

        $term = (int) $request->get('term', 1);
        $year = $request->get('year', date('Y'));

        $termPercentages = TermPercentage::orderBy('term_number')->get()->keyBy('term_number');
        $termPct = $termPercentages->get($term);
        $percent = $termPct ? (float) $termPct->percent_paid : 25;

        $studentIds = $students->pluck('id')->toArray();
        $totalsPaid = Payment::whereIn('student_id', $studentIds)
            ->where('term_number', $term)
            ->where('year', $year)
            ->selectRaw('student_id, SUM(amount) as total')
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $paymentsByStudentTerm = Payment::whereIn('student_id', $studentIds)
            ->where('year', $year)
            ->selectRaw('student_id, term_number, SUM(amount) as total')
            ->groupBy('student_id', 'term_number')
            ->get();

        $paidPerStudentTerm = [];
        foreach ($paymentsByStudentTerm as $row) {
            $key = $row->student_id . '-' . $row->term_number;
            $paidPerStudentTerm[$key] = (float) $row->total;
        }

        foreach ($students as $student) {
            $feeAmount = (float) ($student->fee_amount ?? 0);
            $required = $feeAmount * ($percent / 100);
            $paid = (float) ($totalsPaid->get($student->id) ?? 0);
            $student->below_required = $required > 0 && $paid < $required;

            $paymentLevel = [];
            for ($t = 1; $t <= 4; $t++) {
                $termReq = $termPercentages->has($t)
                    ? $feeAmount * ((float) $termPercentages->get($t)->percent_paid / 100)
                    : 0;
                $termPaid = (float) ($paidPerStudentTerm[$student->id . '-' . $t] ?? 0);
                $paymentLevel[$t] = $termReq <= 0 || $termPaid >= $termReq;
            }
            $student->payment_level = $paymentLevel;
        }

        return view('students.index', compact('students', 'classes', 'term', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'exists:classes,id'],
            'year' => ['required', 'string', 'max:50'],
            'student_type' => ['required', 'in:day,boarding'],
            'fee_amount' => ['required'],
            'contact' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
        ]);

        $feeRaw = is_string($request->input('fee_amount')) ? str_replace(',', '', $request->input('fee_amount')) : $request->input('fee_amount');
        $fee = max(0, (float) $feeRaw);

        $data = $validated;
        $data['fee_amount'] = $fee;

        Student::create($data);

        return redirect()->route('students.index')->with('success', __('Mwanafunzi ameongezwa.'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'exists:classes,id'],
            'year' => ['required', 'string', 'max:50'],
            'student_type' => ['required', 'in:day,boarding'],
            'fee_amount' => ['required'],
            'contact' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
        ]);

        $feeRaw = is_string($request->input('fee_amount')) ? str_replace(',', '', $request->input('fee_amount')) : $request->input('fee_amount');
        $fee = max(0, (float) $feeRaw);

        $data = $validated;
        $data['fee_amount'] = $fee;

        $student->update($data);

        return redirect()->route('students.index')->with('success', __('Mwanafunzi amesasishwa.'));
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', __('Mwanafunzi amefutwa.'));
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);

        Student::whereIn('id', $validated['student_ids'])->delete();

        $n = count($validated['student_ids']);
        return redirect()->route('students.index')->with('success', __('Wanafunzi :n wamefutwa.', ['n' => $n]));
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'year' => ['required', 'string', 'max:50'],
            'class_id' => ['required', 'exists:classes,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'],
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $classId = (int) $validated['class_id'];
        $year = $validated['year'];
        $count = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0 && $this->isHeaderRow($row)) {
                continue;
            }
            $fullname = trim((string) ($row[0] ?? ''));
            if ($fullname === '') {
                continue;
            }
            $contact = trim((string) ($row[1] ?? ''));
            $email = trim((string) ($row[2] ?? ''));
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = '';
            }
            $studentType = $this->normalizeStudentType(trim((string) ($row[3] ?? '')));
            $feeRaw = trim((string) ($row[4] ?? ''));
            $feeRaw = str_replace(',', '', $feeRaw);
            $fee = $feeRaw !== '' ? max(0, (float) $feeRaw) : 0;
            Student::create([
                'fullname' => $fullname,
                'class_id' => $classId,
                'year' => $year,
                'student_type' => $studentType,
                'fee_amount' => $fee,
                'contact' => $contact !== '' ? $contact : null,
                'email' => $email !== '' ? $email : null,
            ]);
            $count++;
        }

        return redirect()->route('students.index')->with('success', __('Wanafunzi :count wameongezwa.', ['count' => $count]));
    }

    private function isHeaderRow(array $row): bool
    {
        $first = strtolower(trim((string) ($row[0] ?? '')));
        return $first === 'fullname' || $first === 'jina' || $first === 'name' || $first === 'jina kamili';
    }

    private function normalizeStudentType(string $value): string
    {
        $v = strtolower(trim($value));
        if (in_array($v, ['boarding', 'b', 'bording', 'boading'], true)) {
            return Student::TYPE_BOARDING;
        }
        return Student::TYPE_DAY; // default day
    }

    public function downloadSample(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sample');
        $sheet->setCellValue('A1', 'fullname');
        $sheet->setCellValue('B1', 'contact');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'student_type');
        $sheet->setCellValue('E1', 'fee_amount');
        $sheet->setCellValue('A2', 'John Doe');
        $sheet->setCellValue('B2', '0712345678');
        $sheet->setCellValue('C2', 'john@example.com');
        $sheet->setCellValue('D2', 'day');
        $sheet->setCellValue('E2', '500000');
        $sheet->setCellValue('A3', 'Jane Smith');
        $sheet->setCellValue('B3', '');
        $sheet->setCellValue('C3', 'jane@school.com');
        $sheet->setCellValue('D3', 'boarding');
        $sheet->setCellValue('E3', '800000');

        $writer = new Xlsx($spreadsheet);
        $filename = 'students_sample.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Student::with('classe')->orderBy('fullname');

        $search = $request->get('search');
        if ($search && is_string($search)) {
            $term = trim($search);
            if ($term !== '') {
                $query->where(function ($q) use ($term) {
                    $q->where('students.fullname', 'like', '%' . $term . '%')
                        ->orWhere('students.year', 'like', '%' . $term . '%')
                        ->orWhereHas('classe', function ($q2) use ($term) {
                            $q2->where('name', 'like', '%' . $term . '%');
                        });
                });
            }
        }

        $students = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('Wanafunzi'));

        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', __('Jina kamili'));
        $sheet->setCellValue('C1', __('Darasa'));
        $sheet->setCellValue('D1', __('Mwaka'));
        $sheet->setCellValue('E1', __('Aina'));
        $sheet->setCellValue('F1', __('Simu'));
        $sheet->setCellValue('G1', __('Barua pepe'));

        $row = 2;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student->fullname);
            $sheet->setCellValue('C' . $row, $student->classe->name);
            $sheet->setCellValue('D' . $row, $student->year);
            $sheet->setCellValue('E' . $row, $student->student_type === \App\Models\Student::TYPE_BOARDING ? __('Boarding') : __('Day'));
            $sheet->setCellValue('F' . $row, $student->contact ?? '');
            $sheet->setCellValue('G' . $row, $student->email ?? '');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'wanafunzi_' . date('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
