<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS.co.tz API
    |--------------------------------------------------------------------------
    | Tumia API key na sender ID kutoka sms.co.tz. Weka kwenye .env:
    |   SMS_CO_TZ_API_KEY=your_api_key
    |   SMS_CO_TZ_SENDER_ID=YourSenderId
    */
    'sms_co_tz' => [
        'api_key'   => env('SMS_CO_TZ_API_KEY', 'a60bbf2b-58df-4380-91e5-0c767377a029'),
        'sender_id' => env('SMS_CO_TZ_SENDER_ID', 'HIGHLINK'),
        'url'       => env('SMS_CO_TZ_URL', 'https://www.sms.co.tz/api.php'),
    ],

];
