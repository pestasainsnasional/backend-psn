<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

Route::get('/admin/verify-email/{id}/{hash}', function (Request $request, $id) {
    $user = User::findOrFail($id);

    if ($user->hasVerifiedEmail()) {
        return redirect(filament()->getLoginUrl())->with('verified', true);
    }

    if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return redirect(filament()->getLoginUrl())->with('verified', true);

})->middleware(['signed'])->name('filament.verification.verify');

Route::redirect('/', '/admin/login');

require __DIR__.'/auth.php';
