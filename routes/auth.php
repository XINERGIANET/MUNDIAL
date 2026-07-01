<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PhonePasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PreRegistrationOtpController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::post('register/enviar-codigo', [PreRegistrationOtpController::class, 'send'])
        ->middleware('throttle:5,1')
        ->name('register.send-code');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Recuperación de contraseña por celular (OTP)
    Route::get('forgot-password', [PhonePasswordResetController::class, 'showPhoneForm'])
        ->name('password.request');

    Route::post('forgot-password/enviar-codigo', [PhonePasswordResetController::class, 'sendOtp'])
        ->middleware('throttle:5,1')
        ->name('password.phone.send');

    Route::get('forgot-password/verificar', [PhonePasswordResetController::class, 'showOtpForm'])
        ->name('password.phone.verify');

    Route::post('forgot-password/verificar', [PhonePasswordResetController::class, 'verifyOtp'])
        ->middleware('throttle:5,1');

    Route::post('forgot-password/reenviar', [PhonePasswordResetController::class, 'resendOtp'])
        ->middleware('throttle:5,1')
        ->name('password.phone.resend');

    Route::get('forgot-password/nueva-contrasena', [PhonePasswordResetController::class, 'showNewPasswordForm'])
        ->name('password.phone.new');

    Route::post('forgot-password/nueva-contrasena', [PhonePasswordResetController::class, 'resetPassword'])
        ->name('password.phone.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
