<?php

use App\Http\Controllers\ChatController;
use App\Models\PublicKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('add-message', [ChatController::class, 'insertMessage']);
    Route::post('public-key-update', [PublicKey::class, 'updatePublicKey']);
    Route::get('user-public-key', [PublicKey::class, 'getPublicKey']);
});