
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Actions\Auth\RegisterUserAction; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', function (Request $request, RegisterUserAction $action) {
    $result = $action->execute($request->all());
    
    return response()->json($result);
});