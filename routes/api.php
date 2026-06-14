<?php

use App\Http\Controllers\Api\V1\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('contacts', ContactController::class);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
