<?php

use App\Livewire\Catalog\ProductCrudForm;
use App\Livewire\Catalog\ProductIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
