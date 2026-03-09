<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Payment;
use App\Models\SmsLog;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        if (! preg_match('/^\d{4}$/', $year)) {
            $year = date('Y');
        }

        $classesCount = Classe::count();
        $studentsCount = Student::count();

        $paymentsThisYear = Payment::where('year', $year)->sum('amount');
        $paymentsCountThisYear = Payment::where('year', $year)->count();

        $smsSentCount = SmsLog::where('status', 'sent')->count();

        $recentPayments = Payment::with('student.classe')
            ->where('year', $year)
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'classesCount',
            'studentsCount',
            'paymentsThisYear',
            'paymentsCountThisYear',
            'smsSentCount',
            'recentPayments',
            'year'
        ));
    }
}
