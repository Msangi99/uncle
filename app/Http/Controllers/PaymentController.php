<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\TermPercentage;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $students = Student::with('classe')->orderBy('fullname')->get();
        $recentPayments = Payment::with('student.classe')->latest('paid_at')->latest('id')->take(20)->get();

        return view('payments.index', compact('students', 'recentPayments'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'amount_clean' => is_string($request->input('amount')) ? str_replace(',', '', $request->input('amount')) : $request->input('amount'),
        ]);

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'amount' => ['required'],
            'amount_clean' => ['required', 'numeric', 'min:0.01'],
            'year' => ['required', 'string', 'max:50'],
        ]);

        $amount = (float) $request->input('amount_clean');
        $year = $validated['year'];
        $student = Student::with('classe')->findOrFail($validated['student_id']);

        $termNumber = $this->resolveTermForPayment($student, $year);

        Payment::create([
            'student_id' => $student->id,
            'amount' => $amount,
            'term_number' => $termNumber,
            'year' => $year,
            'paid_at' => now(),
        ]);

        return redirect()->route('payments.index')->with('success', __('Malipo yamehifadhiwa.') . ' ' . __('Msimu') . ' ' . $termNumber);
    }

    /**
     * Determine which term this payment applies to: first term that is not yet fully paid.
     */
    private function resolveTermForPayment(Student $student, string $year): int
    {
        $feeAmount = (float) ($student->fee_amount ?? 0);

        $termPercentages = TermPercentage::orderBy('term_number')->get()->keyBy('term_number');

        $paidPerTerm = Payment::where('student_id', $student->id)
            ->where('year', $year)
            ->selectRaw('term_number, SUM(amount) as total')
            ->groupBy('term_number')
            ->pluck('total', 'term_number');

        for ($t = 1; $t <= 4; $t++) {
            $pct = $termPercentages->get($t);
            $required = $feeAmount > 0 && $pct ? $feeAmount * ((float) $pct->percent_paid / 100) : 0;
            $alreadyPaid = (float) ($paidPerTerm->get($t) ?? 0);
            if ($alreadyPaid < $required) {
                return $t;
            }
        }

        return 4;
    }
}
