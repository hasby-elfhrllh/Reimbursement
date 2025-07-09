<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReimbursementController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', fn () => auth()->user());

    Route::apiResource('reimbursements', ReimbursementController::class);

    Route::get('categories', [CategoryController::class, 'index']);

    Route::get('dashboard', [DashboardController::class, 'index']);
});
