<?php

use App\Actions\Transaction\ValidateMidtransWebhookSignatureAction;
use Illuminate\Support\Facades\Route;
use App\Actions\Logistics\ProcessBiteshipWebhookAction;
use App\Jobs\ProcessMidtransWebhookJob;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
| These routes are CSRF-exempt (excluded in bootstrap/app.php).
| They use raw POST bodies — do NOT wrap in web middleware.
*/

Route::post('/midtrans', function (ValidateMidtransWebhookSignatureAction $action) {
    $payload = request()->json()->all();

    if (!$action->execute($payload)) {
        return response()->json(['message' => 'Invalid Midtrans signature.'], 403);
    }

    ProcessMidtransWebhookJob::dispatch($payload);

    return response()->json(['status' => 'ok']);
})->name('webhook.midtrans');

Route::post('/biteship', function (ProcessBiteshipWebhookAction $action) {
    $payload = request()->json()->all();
    $action($payload);
    return response()->json(['status' => 'ok']);
})->name('webhook.biteship');
