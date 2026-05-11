<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// ── Catalog (Customer-facing) ──────────────────────────────
Route::get('/catalog', \App\Livewire\Catalog\ProductGrid::class)->name('catalog.index');
Route::get('/catalog/{id}', \App\Livewire\Catalog\ProductDetail::class)->name('catalog.show');
