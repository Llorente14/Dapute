<?php

use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Actions\Auth\LogoutUserAction;

Route::get('/', function () {
    return view('home');
});

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
    Route::get('/register', RegisterForm::class)->name('register');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('profile', ['user' => auth()->user()]);
    })->name('profile');



    Route::post('/logout', function (LogoutUserAction $action) {
        $action->execute();
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
// ── Catalog (Customer-facing) ──────────────────────────────
Route::get('/catalog', \App\Livewire\Catalog\ProductGrid::class)->name('catalog.index');
Route::get('/catalog/{id}', \App\Livewire\Catalog\ProductDetail::class)->name('catalog.show');
