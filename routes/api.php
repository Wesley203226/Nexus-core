<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Type API routes
Route::get('/types', [TypeController::class, 'index']);
Route::post('/types', [TypeController::class, 'store']);

// Product API routes
Route::apiResource('products', ProductController::class);