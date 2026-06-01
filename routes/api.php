<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\GenerationController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

// ── Public ──
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ── Authenticated ──
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'changePassword']);

    // AI Generation
    Route::post('/generate/text', [GenerationController::class, 'generateText']);
    Route::post('/generate/image', [GenerationController::class, 'generateImage']);
    Route::post('/generate/code', [GenerationController::class, 'generateCode']);
    Route::post('/generate/template/{template}', [GenerationController::class, 'generateFromTemplate']);

    // History
    Route::get('/generations', [GenerationController::class, 'history']);
    Route::get('/generations/{generation}', [GenerationController::class, 'show']);
    Route::post('/generations/{generation}/favorite', [GenerationController::class, 'toggleFavorite']);
    Route::delete('/generations/{generation}', [GenerationController::class, 'destroy']);

    // Templates
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/templates/categories', [TemplateController::class, 'categories']);
    Route::get('/templates/{template}', [TemplateController::class, 'show']);

    // Billing
    Route::get('/plans', [BillingController::class, 'plans']);
    Route::post('/subscribe', [BillingController::class, 'subscribe']);
    Route::post('/subscription/cancel', [BillingController::class, 'cancel']);
    Route::post('/subscription/resume', [BillingController::class, 'resume']);
    Route::get('/invoices', [BillingController::class, 'invoices']);
    Route::get('/billing/setup-intent', [BillingController::class, 'setupIntent']);

    // ── Admin ──
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::put('/users/{user}', [AdminController::class, 'updateUser']);
        Route::get('/plans', [AdminController::class, 'plans']);
        Route::post('/plans', [AdminController::class, 'storePlan']);
        Route::put('/plans/{plan}', [AdminController::class, 'updatePlan']);
        Route::get('/templates', [AdminController::class, 'templates']);
        Route::post('/templates', [AdminController::class, 'storeTemplate']);
        Route::put('/templates/{template}', [AdminController::class, 'updateTemplate']);
        Route::delete('/templates/{template}', [AdminController::class, 'deleteTemplate']);
    });
});
