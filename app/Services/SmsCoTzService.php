<?php

namespace App\Services;

class SmsCoTzService
{
    public function send(string $destination, string $message): array
    {
        $apiKey = config('sms.sms_co_tz.api_key');
        $senderId = urlencode(config('sms.sms_co_tz.sender_id', ''));
        $url = config('sms.sms_co_tz.url', 'https://www.sms.co.tz/api.php');

        if ($apiKey === '' || $senderId === '') {
            return ['ok' => false, 'detail' => 'SMS API not configured (api_key or sender_id missing).'];
        }

        $destination = preg_replace('/\D/', '', $destination);
        if (strlen($destination) < 9) {
            return ['ok' => false, 'detail' => 'INVALIDNUMBER'];
        }
        if (!str_starts_with($destination, '255')) {
            $destination = '255' . ltrim($destination, '0');
        }

        $message = urlencode($message);
        $query = "do=sms&api_key={$apiKey}&senderid={$senderId}&dest={$destination}&msg={$message}";
        $fullUrl = $url . '?' . $query;

        $context = stream_context_create([
            'http' => ['timeout' => 10],
        ]);
        $fetch = @file_get_contents($fullUrl, false, $context);

        if ($fetch === false) {
            return ['ok' => false, 'detail' => 'Network error'];
        }

        $result = explode(',', trim($fetch));
        $resultStatus = $result[0] ?? '';
        $resultDetail = $result[1] ?? '';
        $resultId = $result[2] ?? null;

        if ($resultStatus === 'OK') {
            return ['ok' => true, 'id' => $resultId];
        }

        return ['ok' => false, 'detail' => $resultDetail ?: 'Unknown error'];
    }
}
