<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\SponsorController;
use App\Http\Controllers\Api\ProfileController;

Route::controller(CompetitionController::class)->group(function () {
    Route::get('/competitions', 'index');
    Route::get('/competitions/{competition}', 'show');
    Route::get('/competition-types', 'getCompetitionTypes');
});

Route::get('/testimonials', [TestimonialController::class, 'index']);

Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/faqs/{id}', [FaqController::class, 'show']);

Route::get('/sponsors', [SponsorController::class, 'index']);

Route::prefix('histories')->group(function() {
    Route::get('/seasons', [HistoryController::class, 'listSeasons']);
    Route::get('/season/latest', [HistoryController::class, 'latestSeason']);
    Route::get('/season/{year}', [HistoryController::class, 'spesificSeason']);
});

Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);


Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('profile')->group(function() {
        Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
        Route::get('/registrations', [ProfileController::class, 'myRegistrations']);
    });

    Route::get('/registrations/{registration}', [ProfileController::class, 'showRegistrationDetail']);

    Route::prefix('step-registration')->group(function () {
        Route::get('/draf/{competition_id}', [RegistrationController::class, 'getDraft']);
        Route::post('/1-tim', [RegistrationController::class, 'storeStep1']);
        Route::post('/2-personil', [RegistrationController::class, 'storeStep2']);
        Route::post('/3-pendamping', [RegistrationController::class, 'storeStep3']);
        Route::get('/payment-code/{registration_id}', [RegistrationController::class, 'getPaymentCode']);
        Route::post('/4-dokumen', [RegistrationController::class, 'storeStep4']);
        Route::post('/finalisasi', [RegistrationController::class, 'finalize']);
    });

});
