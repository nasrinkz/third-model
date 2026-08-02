<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\Admin\{AnswerOptionController, CategoryController, QuestionController};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/loginHome', [AuthController::class, 'loginHome'])->name('login');
    Route::get('/questions', [HomeController::class, 'index']);
    Route::get('/questions/{category}', [HomeController::class, 'byCategory']);
    Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::prefix('categories')->group(function () {
            Route::get('/list', [CategoryController::class, 'index']);
            Route::get('/list/{id}', [CategoryController::class, 'show']);
            Route::get('/{id}/questions', [CategoryController::class, 'getWithQuestions']);
            Route::post('/store', [CategoryController::class, 'store']);
            Route::post('/update/{id}', [CategoryController::class, 'update']);
            Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
            Route::patch('/toggle-active/{id}', [CategoryController::class, 'toggleActive']);
            Route::post('/reorder', [CategoryController::class, 'reorder']);
            Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete']);
        });

        Route::prefix('questions')->group(function () {
            Route::get('/list', [QuestionController::class, 'index']);
            Route::get('/list/{id}', [QuestionController::class, 'show']);
            Route::post('/store', [QuestionController::class, 'store']);
            Route::post('/update/{id}', [QuestionController::class, 'update']);
            Route::delete('/delete/{id}', [QuestionController::class, 'destroy']);
            Route::patch('/toggle-active/{id}', [QuestionController::class, 'toggleActive']);
            Route::get('/stats', [QuestionController::class, 'stats']);
            Route::get('/list/by-category/{categoryId}', [QuestionController::class, 'byCategory']);
        });

        Route::prefix('answer-options')->group(function () {
            Route::get('/list', [AnswerOptionController::class, 'index']);
            Route::get('/list/{id}', [AnswerOptionController::class, 'show']);
            Route::post('/store', [AnswerOptionController::class, 'store']);
            Route::post('/update/{id}', [AnswerOptionController::class, 'update']);
            Route::delete('/delete/{id}', [AnswerOptionController::class, 'destroy']);
            Route::patch('/toggle-active/{id}', [AnswerOptionController::class, 'toggleActive']);
            Route::get('/types', [AnswerOptionController::class, 'types']);
            Route::get('/type/{type}', [AnswerOptionController::class, 'getByType']);
            Route::delete('/type/{type}', [AnswerOptionController::class, 'deleteByType']);
        });
    });
});
