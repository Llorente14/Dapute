<?php

use App\Livewire\Catalog\ProductCrudForm;
use App\Livewire\Catalog\ProductIndex;
use Illuminate\Support\Facades\Route;
use App\Actions\Checkout\FetchBiteshipRatesAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Actions\Auth\LogoutUserAction;
use App\Livewire\CheckoutPage;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use App\Livewire\ProfileForm;
use App\Livewire\Transaction\OrderHistoryPage;
use App\Livewire\Transaction\OrderDetailPage;

Route::get('/', function () {
    return view('home');
});

Route::post('/checkout/rates', function (Request $request, FetchBiteshipRatesAction $action) {
    // Memastikan user sudah auth di level route/middleware
    $request->validate(['postal_code' => 'required']);
    return response()->json($action->execute((string) auth()->id(), $request->postal_code, $request->all(), $request->input('courier_type', 'regular')));
})->middleware('auth');
/*
|--------------------------------------------------------------------------
| Owner / Admin — Product CRUD (SCRUM-36)
|--------------------------------------------------------------------------
| TODO: Tambahkan middleware(['auth', 'role:owner|admin']) saat auth siap.
| Spatie permission sudah terpasang di composer.json.
*/
use App\Livewire\Admin\UserManagement;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/products',              ProductIndex::class)->name('products.index');
    Route::get('/products/create',       ProductCrudForm::class)->name('products.create');
    Route::get('/products/{productId}/edit', ProductCrudForm::class)->name('products.edit');
    Route::get('/users',                 UserManagement::class)->name('users.index');
});
// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
    Route::get('/register', RegisterForm::class)->name('register');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/checkout', CheckoutPage::class)->name('checkout');
    Route::get('/order', OrderHistoryPage::class)->name('orders.index');
    Route::get('/orders', OrderHistoryPage::class)->name('orders.index.alias');
    Route::get('/order/{id}', OrderDetailPage::class)->name('orders.show');
    Route::get('/orders/{id}', OrderDetailPage::class)->name('orders.show.alias');
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
