<?php

use App\Livewire\Catalog\ProductCrudForm;
use App\Livewire\Catalog\ProductIndex;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Actions\Auth\LogoutUserAction;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use App\Livewire\ProfileForm;

Route::get('/', function () {
    return view('home');
});

/*
|--------------------------------------------------------------------------
| Owner / Admin — Product CRUD (SCRUM-36)
|--------------------------------------------------------------------------
| TODO: Tambahkan middleware(['auth', 'role:owner|admin']) saat auth siap.
| Spatie permission sudah terpasang di composer.json.
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/products',              ProductIndex::class)->name('products.index');
    Route::get('/products/create',       ProductCrudForm::class)->name('products.create');
    Route::get('/products/{productId}/edit', ProductCrudForm::class)->name('products.edit');
});
// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
    Route::get('/register', RegisterForm::class)->name('register');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', ProfileForm::class)->name('profile');


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
