<?php

use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Actions\Auth\LogoutUserAction;
use App\Livewire\ProfileForm;

Route::get('/', function () {
    return view('welcome');
});

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
    Route::get('/register', RegisterForm::class)->name('register');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', ProfileForm::class)->name('profile');

    Route::get('/catalog', function () {
        // TODO: SCRUM-XX — Halaman Katalog
        return view('welcome'); // Placeholder
    })->name('catalog');

    Route::post('/logout', function (LogoutUserAction $action) {
        $action->execute();
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
