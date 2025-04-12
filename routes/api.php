<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QueueController;


// Публичные роуты
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


// Защищенные роуты
Route::middleware('auth:api')->group(function () {
    // Очереди
    Route::get('/queues', [QueueController::class, 'listQueues']);
    Route::post('/queues/{queue}/join', [QueueController::class, 'joinQueue']);
    Route::get('/status', [QueueController::class, 'getStatus']);
    Route::delete('/queues/{queue}/cancel', [QueueController::class, 'cancelQueue']);
});