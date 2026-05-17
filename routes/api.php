
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Actions\Auth\RegisterUserAction;
use App\Actions\Transaction\ValidateMidtransWebhookSignatureAction;
use App\Jobs\ProcessMidtransWebhookJob;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', function (Request $request, RegisterUserAction $action) {
    $result = $action->execute($request->all());
    
    return response()->json($result);
});

Route::post('/webhook/midtrans', function (Request $request, ValidateMidtransWebhookSignatureAction $action) {
    $payload = $request->all();

    if (!$action->execute($payload)) {
        return response()->json(['message' => 'Invalid Midtrans signature.'], 403);
    }

    ProcessMidtransWebhookJob::dispatch($payload);

    return response()->json(['status' => 'ok']);
})->name('api.webhook.midtrans');
