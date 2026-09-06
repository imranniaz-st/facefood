<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\WordPressSyncController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/settings', [SettingController::class, 'index']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::get('/deals', [DealController::class, 'index']);
Route::get('/deals/{deal}', [DealController::class, 'show']);

Route::post('/orders/quote', [OrderController::class, 'quote']);

Route::middleware('wordpress.sync')->prefix('wordpress')->group(function () {
    Route::get('/catalog', [WordPressSyncController::class, 'catalog']);
    Route::post('/link', [WordPressSyncController::class, 'link']);
    Route::post('/bulk-link', [WordPressSyncController::class, 'bulkLink']);
    Route::post('/products/upsert', [WordPressSyncController::class, 'upsertProduct']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [ProfileController::class, 'show']);
    Route::put('/user', [ProfileController::class, 'update']);
    Route::patch('/user', [ProfileController::class, 'update']);

    Route::apiResource('addresses', AddressController::class);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::delete('/favorites/{product}', [FavoriteController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::put('/settings', [SettingController::class, 'update']);
    Route::patch('/settings', [SettingController::class, 'update']);

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::patch('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::patch('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::post('/deals', [DealController::class, 'store']);
    Route::put('/deals/{deal}', [DealController::class, 'update']);
    Route::patch('/deals/{deal}', [DealController::class, 'update']);
    Route::delete('/deals/{deal}', [DealController::class, 'destroy']);
});
