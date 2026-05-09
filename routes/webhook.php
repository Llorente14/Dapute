<?php

use Illuminate\Support\Facades\Route;
use App\Actions\Transaction\ProcessMidtransWebhookAction;
use App\Actions\Logistics\ProcessBiteshipWebhookAction;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
| These routes are CSRF-exempt (excluded in bootstrap/app.php).
| They use raw POST bodies — do NOT wrap in web middleware.
*/

Route::post('/midtrans', function (ProcessMidtransWebhookAction $action) {
    $payload = request()->json()->all();
    $action($payload);
    return response()->json(['status' => 'ok']);
})->name('webhook.midtrans');

Route::post('/biteship', function (ProcessBiteshipWebhookAction $action) {
    $payload = request()->json()->all();
    $action($payload);
    return response()->json(['status' => 'ok']);
})->name('webhook.biteship');
