<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TournamentRegistrationController;
use App\Http\Controllers\UserDashboardController;
use App\Models\FootballMatch;
use Illuminate\Support\Facades\Route;

Route::model('match', FootballMatch::class);

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/torneos', [PublicController::class, 'tournaments'])->name('tournaments.index');
Route::get('/torneos/{tournament}/ranking', [PublicController::class, 'ranking'])->name('tournaments.ranking');

Route::get('/dashboard', [UserDashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/verificar-celular', [PhoneVerificationController::class, 'show'])->name('phone.verify');
    Route::post('/verificar-celular', [PhoneVerificationController::class, 'store'])->middleware('throttle:5,1')->name('phone.verify.store');
    Route::post('/verificar-celular/reenviar', [PhoneVerificationController::class, 'resend'])->middleware('throttle:3,1')->name('phone.verify.resend');
    Route::post('/torneos/{tournament}/inscripcion', [TournamentRegistrationController::class, 'store'])->name('tournaments.register');
    Route::post('/torneos/{tournament}/pronosticos', [PredictionController::class, 'bulkStore'])->name('predictions.bulk-store');
    Route::post('/partidos/{match}/pronostico', [PredictionController::class, 'store'])->name('predictions.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
