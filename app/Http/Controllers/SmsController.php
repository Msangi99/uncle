<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\SmsLog;
use App\Models\Student;
use App\Services\SmsCoTzService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classe')->orderBy('fullname')->get();
        $classes = Classe::orderBy('name')->get();

        $year = $request->get('year', date('Y'));
        $classId = $request->get('class_id');

        if ($classId) {
            $students = $students->where('class_id', (int) $classId);
        }
        if ($year !== '') {
            $students = $students->where('year', $year);
        }
        $students = $students->values();

        return view('sms.index', compact('students', 'classes', 'year', 'classId'));
    }

    public function send(Request $request, SmsCoTzService $sms)
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $studentIds = $validated['student_ids'];
        $template = $validated['message'];

        $students = Student::with('classe')->whereIn('id', $studentIds)->get();
        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($students as $student) {
            $phone = $student->contact ?? null;
            if (!$phone || trim($phone) === '') {
                $errors[] = __('Mwanafunzi :name hana nambari ya simu.', ['name' => $student->fullname]);
                $failed++;
                SmsLog::create([
                    'user_id' => auth()->id(),
                    'student_id' => $student->id,
                    'destination' => null,
                    'message' => $this->replaceVariables($template, $student),
                    'status' => 'failed',
                    'response_detail' => __('Hakuna nambari ya simu'),
                    'sent_at' => now(),
                ]);
                continue;
            }

            $message = $this->replaceVariables($template, $student);
            $result = $sms->send($phone, $message);

            if ($result['ok']) {
                $sent++;
                SmsLog::create([
                    'user_id' => auth()->id(),
                    'student_id' => $student->id,
                    'destination' => $phone,
                    'message' => $message,
                    'status' => 'sent',
                    'response_detail' => $result['id'] ?? null,
                    'sent_at' => now(),
                ]);
            } else {
                $failed++;
                $errors[] = $student->fullname . ': ' . ($result['detail'] ?? 'Error');
                SmsLog::create([
                    'user_id' => auth()->id(),
                    'student_id' => $student->id,
                    'destination' => $phone,
                    'message' => $message,
                    'status' => 'failed',
                    'response_detail' => $result['detail'] ?? null,
                    'sent_at' => now(),
                ]);
            }
        }

        $message = __('SMS :sent zimetumwa.', ['sent' => $sent]);
        if ($failed > 0) {
            $message .= ' ' . __('Shida :n.', ['n' => $failed]);
        }

        return redirect()->route('sms.index')->with('sms_result', [
            'success' => $sent > 0,
            'message' => $message,
            'sent' => $sent,
            'failed' => $failed,
            'errors' => $errors,
        ]);
    }

    private function replaceVariables(string $text, Student $student): string
    {
        $replace = [
            '@studentname' => $student->fullname,
            '@class' => $student->classe->name ?? '',
            '@year' => $student->year ?? '',
            '@darasa' => $student->classe->name ?? '',
            '@mwaka' => $student->year ?? '',
        ];

        return str_replace(array_keys($replace), array_values($replace), $text);
    }

    public function log(Request $request)
    {
        $logs = SmsLog::with(['student', 'user'])
            ->orderByDesc('sent_at')
            ->paginate(20);

        return view('sms.log', compact('logs'));
    }

    public function clearLog()
    {
        SmsLog::query()->delete();

        return redirect()->route('sms.log')->with('success', __('Historia ya SMS imefutwa.'));
    }
}
