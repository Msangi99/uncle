<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\PaymentTypeSetting;
use App\Models\Student;
use App\Models\StudentOtherPayment;
use Illuminate\Http\Request;

class OtherPaymentsController extends Controller
{
    public function index(Request $request)
    {
        $term = (int) $request->get('term', 1);
        $year = $request->get('year', date('Y'));

        $students = Student::with('classe')->orderBy('fullname')->get();
        $settings = PaymentTypeSetting::getInstance();
        $typeKeys = array_keys(PaymentTypeSetting::typeKeys());

        $paidByStudentType = StudentOtherPayment::where('term_number', $term)
            ->where('year', $year)
            ->get()
            ->groupBy('student_id');

        foreach ($students as $student) {
            $requiredTotal = 0;
            $paidTotal = 0;
            $details = [];

            $isExamClass = $student->classe && $student->classe->is_exam_class;

            foreach ($typeKeys as $key) {
                if ($key === 'pesa_ya_mtihani_wa_taifa' && ! $isExamClass) {
                    continue;
                }
                $required = (float) ($settings->{$key} ?? 0);
                $paid = 0;
                $studentPayments = $paidByStudentType->get($student->id);
                if ($studentPayments) {
                    $row = $studentPayments->firstWhere('payment_type', $key);
                    $paid = $row ? (float) $row->amount : 0;
                }
                $requiredTotal += $required;
                $paidTotal += $paid;
                $details[$key] = ['required' => $required, 'paid' => $paid];
            }

            $student->other_payment_required = $requiredTotal;
            $student->other_payment_paid = $paidTotal;
            $student->other_payment_below = $requiredTotal > 0 && $paidTotal < $requiredTotal;
            $student->other_payment_details = $details;
        }

        return view('other-payments.index', compact('students', 'term', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'term_number' => ['required', 'integer', 'min:1', 'max:4'],
            'year' => ['required', 'string', 'max:50'],
            'payment_type' => ['required', 'string', 'in:' . implode(',', array_keys(PaymentTypeSetting::typeKeys()))],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $classe = $student->classe;
        if ($validated['payment_type'] === 'pesa_ya_mtihani_wa_taifa' && (! $classe || ! $classe->is_exam_class)) {
            return redirect()->back()->with('error', __('Darasa la mwanafunzi si darasa la mtihani.'));
        }

        $row = StudentOtherPayment::firstOrNew([
            'student_id' => $validated['student_id'],
            'term_number' => $validated['term_number'],
            'year' => $validated['year'],
            'payment_type' => $validated['payment_type'],
        ]);
        $row->amount = (float) ($row->amount ?? 0) + (float) $validated['amount'];
        $row->paid_at = $row->paid_at ?? now();
        $row->save();

        return redirect()->route('other-payments.index', [
            'term' => $validated['term_number'],
            'year' => $validated['year'],
        ])->with('success', __('Malipo yamehifadhiwa.'));
    }
}
