<?php

use Illuminate\Support\Facades\Route;
use App\Actions\Checkout\FetchBiteshipRatesAction;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/checkout/rates', function (Request $request, FetchBiteshipRatesAction $action) {
    // Memastikan user sudah auth di level route/middleware
    $request->validate(['postal_code' => 'required']);
    return response()->json($action->execute(auth()->id(), $request->postal_code));
})->middleware('auth');