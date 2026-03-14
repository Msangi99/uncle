<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\TermPercentage;
use Illuminate\Http\Request;

class StudentInfoController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classe')->orderBy('fullname')->get();

        $student = null;
        $adaPayments = [];
        $summaryByYear = [];

        $studentId = $request->get('student_id');
        if ($studentId && is_numeric($studentId)) {
            $student = Student::with('classe')->find($studentId);
            if ($student) {
                $adaPayments = Payment::where('student_id', $student->id)
                    ->orderBy('year')
                    ->orderBy('term_number')
                    ->orderBy('paid_at')
                    ->get();

                $feeAmount = (float) ($student->fee_amount ?? 0);
                $termPercentages = TermPercentage::orderBy('term_number')->get()->keyBy('term_number');

                $byYear = $adaPayments->groupBy('year');
                foreach ($byYear as $year => $payments) {
                    $totalPaid = $payments->sum('amount');
                    $requiredPerTerm = [];
                    for ($t = 1; $t <= 4; $t++) {
                        $pct = $termPercentages->get($t);
                        $requiredPerTerm[$t] = $feeAmount > 0 && $pct
                            ? $feeAmount * ((float) $pct->percent_paid / 100)
                            : 0;
                    }
                    $totalRequired = array_sum($requiredPerTerm);
                    $summaryByYear[$year] = [
                        'total_required' => $totalRequired,
                        'total_paid' => (float) $totalPaid,
                        'deni' => max(0, $totalRequired - $totalPaid),
                        'per_term_required' => $requiredPerTerm,
                        'payments' => $payments,
                    ];
                }
            }
        }

        return view('student-info.index', compact('students', 'student', 'adaPayments', 'summaryByYear'));
    }
}
