<?php

namespace App\Http\Controllers;

use App\Models\ClassFee;
use App\Models\Classe;
use App\Models\Payment;
use App\Models\Student;
use App\Models\TermPercentage;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $classes = Classe::orderBy('name')->get();
        $year = $request->get('year', date('Y'));
        $classId = $request->get('class_id');

        return view('report.index', compact('classes', 'year', 'classId'));
    }

    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'year' => ['required', 'string', 'max:50'],
        ]);

        $classId = (int) $request->class_id;
        $year = $request->year;

        $students = Student::with('classe')
            ->where('class_id', $classId)
            ->where('year', $year)
            ->orderBy('fullname')
            ->get();

        $classFees = ClassFee::where('class_id', $classId)->get()->keyBy('class_id');
        $termPercentages = TermPercentage::orderBy('term_number')->get()->keyBy('term_number');

        $studentIds = $students->pluck('id')->toArray();
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ripoti');

        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Jina');
        $sheet->setCellValue('C1', 'Darasa');
        $sheet->setCellValue('D1', 'Mwaka');
        $sheet->setCellValue('E1', 'M1');
        $sheet->setCellValue('F1', 'M2');
        $sheet->setCellValue('G1', 'M3');
        $sheet->setCellValue('H1', 'M4');
        $sheet->setCellValue('I1', 'Ada inayotakiwa');
        $sheet->setCellValue('J1', 'Alilolipa');
        $sheet->setCellValue('K1', 'Deni');

        $row = 2;
        foreach ($students as $index => $student) {
            $fee = $classFees->get($student->class_id);
            $totalRequired = $fee ? (float) $fee->amount : 0;
            $totalPaid = 0;
            $m1 = $m2 = $m3 = $m4 = '✗';

            for ($t = 1; $t <= 4; $t++) {
                $termReq = $fee && $termPercentages->has($t)
                    ? (float) $fee->amount * ((float) $termPercentages->get($t)->percent_paid / 100)
                    : 0;
                $termPaid = (float) ($paidPerStudentTerm[$student->id . '-' . $t] ?? 0);
                $totalPaid += $termPaid;
                $status = $termReq <= 0 || $termPaid >= $termReq ? '✓' : '✗';
                if ($t === 1) $m1 = $status;
                elseif ($t === 2) $m2 = $status;
                elseif ($t === 3) $m3 = $status;
                else $m4 = $status;
            }

            $deni = max(0, $totalRequired - $totalPaid);

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student->fullname);
            $sheet->setCellValue('C' . $row, $student->classe->name);
            $sheet->setCellValue('D' . $row, $student->year);
            $sheet->setCellValue('E' . $row, $m1);
            $sheet->setCellValue('F' . $row, $m2);
            $sheet->setCellValue('G' . $row, $m3);
            $sheet->setCellValue('H' . $row, $m4);
            $sheet->setCellValue('I' . $row, $totalRequired);
            $sheet->setCellValue('J' . $row, $totalPaid);
            $sheet->setCellValue('K' . $row, $deni);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'ripoti_wanafunzi_' . $year . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
