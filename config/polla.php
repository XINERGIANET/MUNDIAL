<?php

return [
    'otp_provider' => env('OTP_PROVIDER', 'log'),
    'otp_channel_default' => env('OTP_CHANNEL_DEFAULT', 'whatsapp'),
    'otp_expires_minutes' => (int) env('OTP_EXPIRES_MINUTES', 10),
    'otp_resend_seconds' => (int) env('OTP_RESEND_SECONDS', 60),
    'otp_max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),
    'payment_whatsapp_number' => env('PAYMENT_WHATSAPP_NUMBER'),
    'payment_message' => env('PAYMENT_MESSAGE', 'Hola, soy {nombre}, mi celular es {celular}. Quiero participar en la polla del torneo {torneo}. Por favor enviame los medios de pago.'),
    'pending_result_after_hours' => (int) env('PENDING_RESULT_AFTER_HOURS', 2),
];
