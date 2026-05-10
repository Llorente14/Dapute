<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use App\Livewire\ProfileForm;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', LoginForm::class)->middleware('guest')->name('login');
Route::get('/register', RegisterForm::class)->middleware('guest')->name('register');

Route::get('/profile', ProfileForm::class)->middleware('auth')->name('profile');
