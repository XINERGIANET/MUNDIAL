<?php

return [
    'otp_provider' => env('OTP_PROVIDER', 'sms'),
    'otp_channel_default' => env('OTP_CHANNEL_DEFAULT', 'sms'),
    'otp_default_country_code' => env('OTP_DEFAULT_COUNTRY_CODE', '51'),
    'otp_expires_minutes' => (int) env('OTP_EXPIRES_MINUTES', 10),
    'otp_resend_seconds' => (int) env('OTP_RESEND_SECONDS', 60),
    'otp_max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),
    'otp_queue' => (bool) env('OTP_QUEUE', false),
    'twilio_account_sid' => env('TWILIO_ACCOUNT_SID'),
    'twilio_auth_token' => env('TWILIO_AUTH_TOKEN'),
    'twilio_sms_from' => env('TWILIO_SMS_FROM'),
    'twilio_whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    'payment_whatsapp_number' => env('PAYMENT_WHATSAPP_NUMBER'),
    'payment_message' => env('PAYMENT_MESSAGE', 'Hola, soy {nombre}, mi celular es {celular}. Quiero participar en la polla del torneo {torneo}. Por favor enviame los medios de pago.'),
    'pending_result_after_hours' => (int) env('PENDING_RESULT_AFTER_HOURS', 2),
];
