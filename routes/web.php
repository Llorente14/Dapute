<?php

use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', RegisterForm::class)->name('register');
Route::get('/login', LoginForm::class)->name('login');
