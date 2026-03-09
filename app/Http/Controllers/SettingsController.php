<?php

namespace App\Http\Controllers;

use App\Models\ClassFee;
use App\Models\Classe;
use App\Models\PaymentTypeSetting;
use App\Models\SmsCredential;
use App\Models\TermPercentage;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $classes = Classe::orderBy('name')->get();
        $classFees = ClassFee::whereIn('class_id', $classes->pluck('id'))->get()->keyBy('class_id');
        $termPercentages = TermPercentage::orderBy('term_number')->get()->keyBy('term_number');
        $smsCredential = SmsCredential::getInstance();
        $paymentTypeSettings = PaymentTypeSetting::getInstance();

        return view('settings.index', compact('classes', 'classFees', 'termPercentages', 'smsCredential', 'paymentTypeSettings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ada' => ['required', 'array'],
            'ada.*' => ['nullable', 'string'],
            'term_percent' => ['required', 'array'],
            'term_percent.*' => ['required', 'numeric', 'min:0', 'max:100'],
            'payment_types' => ['sometimes', 'array'],
            'payment_types.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($request->input('ada', []) as $classId => $amount) {
            if ($classId === '' || ! is_numeric($classId)) {
                continue;
            }
            $amount = is_string($amount) ? str_replace(',', '', $amount) : $amount;
            $amount = (float) $amount;
            if ($amount < 0) {
                continue;
            }
            ClassFee::updateOrCreate(
                ['class_id' => $classId],
                ['amount' => $amount]
            );
        }

        foreach ($request->input('term_percent', []) as $termNumber => $percent) {
            $termNumber = (int) $termNumber;
            if ($termNumber < 1 || $termNumber > 4) {
                continue;
            }
            TermPercentage::updateOrCreate(
                ['term_number' => $termNumber],
                ['percent_paid' => (float) $percent]
            );
        }

        $paymentTypes = $request->input('payment_types', []);
        $settings = PaymentTypeSetting::getInstance();
        foreach (array_keys(PaymentTypeSetting::typeKeys()) as $key) {
            $val = $paymentTypes[$key] ?? null;
            $val = is_string($val) ? str_replace(',', '', $val) : $val;
            $val = (float) ($val ?? 0);
            if ($val < 0) {
                $val = 0;
            }
            $settings->{$key} = $val;
        }
        $settings->save();

        return redirect()->route('settings.index')->with('success', __('Mipangilio imehifadhiwa.'));
    }

    public function storeSms(Request $request)
    {
        $request->validate([
            'sms_api_key' => ['nullable', 'string', 'max:255'],
            'sms_sender_id' => ['nullable', 'string', 'max:64'],
            'sms_url' => ['nullable', 'string', 'url', 'max:255'],
        ]);

        $cred = SmsCredential::firstOrNew([]);
        $cred->api_key = $request->input('sms_api_key') ?: null;
        $cred->sender_id = $request->input('sms_sender_id') ?: null;
        $cred->url = $request->input('sms_url') ?: null;
        $cred->save();

        return redirect()->route('settings.index')->with('success', __('Siri za SMS zimehifadhiwa.'));
    }
}
