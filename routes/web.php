<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', LoginForm::class)->middleware('guest')->name('login');
Route::get('/register', RegisterForm::class)->middleware('guest')->name('register');
