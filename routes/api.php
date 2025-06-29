<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\DraftRegistrationController;
use App\Http\Controllers\Api\FinalizeRegistrationController;


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

Route::middleware('auth:sanctum')->post('/registrasi', [RegistrationController::class, 'store']);

Route::moddleware('auth:santum')->group(function() {
    Route ::post('/draft', [DraftRegistrationController::class, 'save']);
    Route::get('/drafts/{competition_id}', [DraftRegistrationController::class, 'get']);

     Route::post('/registrations/finalize', [FinalizeRegistrationController::class, 'store']);

});