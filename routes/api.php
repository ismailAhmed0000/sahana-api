<?php

use App\Http\Controllers\Api\AttachableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisatserController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\SafePointController;
use App\Http\Controllers\SosController;
use Illuminate\Support\Facades\Route;

Route::apiResource('sos', SosController::class);
Route::apiResource('disaster', DisatserController::class);
Route::apiResource('points', PointsController::class);
Route::apiResource('safe-points', SafePointController::class);
Route::apiResource('attachable', AttachableController::class);
Route::get('/approved-ngos', [AuthController::class, 'getApprovedNgos']);
Route::patch('/users/{user}/approve', [AuthController::class, 'approve']);
Route::patch('/users/{user}/disapprove', [AuthController::class, 'disapprove']);
Route::get('/get-all-ngo', [AuthController::class, 'ngos']);

Route::post('/ngo/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/ngo/login', [AuthController::class, 'ngoLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/overview', [PointsController::class, 'overView']);
    Route::post('/ngo/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/get-all-ngo', [AuthController::class, 'ngos']);
    Route::patch('/users/{user}/approve', [AuthController::class, 'approve']);
    Route::patch('/users/{user}/disapprove', [AuthController::class, 'disapprove']);
    Route::delete('/resource/{resource}/delete', [SafePointController::class, 'deleteResource']);
});
