<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Api\FaqController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

Route::controller(CompetitionController::class)->group(function () {
    Route::get('/competitions', 'index');
    Route::get('/competitions/{competition}', 'show');
    Route::get('/competition-types', 'getCompetitionTypes');
});

Route::controller(TestimonialController::class)->group(function() {
    Route::get('/testimonials', 'index');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->prefix('step-registration')->group(function () {
    Route::get('/draf/{competition_id}', [RegistrationController::class, 'getDraft']);
    Route::post('/1-tim', [RegistrationController::class, 'storeStep1']);
    Route::post('/2-personil', [RegistrationController::class, 'storeStep2']);
    Route::post('/3-pendamping', [RegistrationController::class, 'storeStep3']);
    Route::post('/4-dokumen', [RegistrationController::class, 'storeStep4']);
    Route::post('/finalisasi', [RegistrationController::class, 'finalize']);
});

Route::prefix('histories')->group(function() {
    Route::get('/seasons', [HistoryController::class, 'listSeasons']);
    Route::get('/season/latest', [HistoryController::class, 'latestSeason']);
    Route::get('/season/{year}', [HistoryController::class, 'spesificSeason']);
});

Route::controller(FaqController::class)->group(function() {
    Route::get('/faqs', 'index');
    Route::get('/faqs/{id}', 'show');
});