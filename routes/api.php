<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::group(['prefix' => 'password'], function() {
    Route::post('reset', [AuthController::class, 'resetPassword']);
    Route::post('forgot', [AuthController::class, 'forgotPassword']);
});
Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('user', [AuthController::class, 'user']);
    Route::any('logout', [AuthController::class, 'logout']);
});

Route::group(['prefix' =>'subscriptions'], function() {
    Route::get('/', [SubscriptionController::class, 'index']);
    Route::get('/{id}', [SubscriptionController::class, 'show']);
    Route::post('/verify', [SubscriptionController::class, 'verify']);
    Route::post('/', [SubscriptionController::class, 'store']);
    Route::middleware('auth:sanctum')->group(function() {
        Route::match(['POST', 'PUT', 'PATCH'],'/{id}', [SubscriptionController::class, 'update']);
        Route::delete('/{id}', [SubscriptionController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum', 'admin']], function() {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::match(['POST', 'PUT', 'PATCH'],'/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::group(['prefix' => 'settings'], function() {
    Route::get('paystack', [ SettingsController::class, 'paystack' ]);
});

Route::any('/', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => "welcome to our api base url ".$request->url(),
        'data' => [
            'request' => array_merge($request->all(), ['path' => $request->getPathInfo()]),
            'authorization' => $request->header('Authorization'),
            'user_agent' => $request->userAgent(),
            'has_session' => $request->hasPreviousSession(),
        ]
    ]);
});

Route::fallback(function (Request $request) {
    return response()->json([
        'success' => false,
        'message' => 'route does not exist',
        'data' => [
            'request' => array_merge($request->all(), ['path' => $request->getPathInfo()]),
            'authorization' => $request->header('Authorization'),
            'response' => null,
        ]
    ]);
});

