<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\Admin\{CategoryController, QuestionController, AnswerOptionController};
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1/')->group(function () {
    Route::get('/questions', [HomeController::class, 'index']);
    Route::get('/questions/{category}', [HomeController::class, 'byCategory']);

    // مدیریت دسته‌بندی‌ها
    Route::prefix('admin/categories')->group(function () {
        Route::get('/list', [CategoryController::class, 'index']);
        Route::get('/list/{id}', [CategoryController::class, 'show']);
        Route::post('/store', [CategoryController::class, 'store']);
        Route::post('/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [CategoryController::class, 'toggleActive']);
    });

    // مدیریت سوالات
    Route::prefix('admin/questions')->group(function () {
        Route::get('/list', [QuestionController::class, 'index']);
        Route::get('/list/{id}', [QuestionController::class, 'show']);
        Route::post('/store', [QuestionController::class, 'store']);
        Route::put('/update/{id}', [QuestionController::class, 'update']);
        Route::delete('/delete/{id}', [QuestionController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [QuestionController::class, 'toggleActive']);
        Route::get('/list/by-category/{categoryId}', [QuestionController::class, 'byCategory']);
    });

    // مدیریت گزینه‌های پاسخ
    Route::prefix('admin/answer-options')->group(function () {
        Route::get('/list', [AnswerOptionController::class, 'index']);
        Route::get('/list/{id}', [AnswerOptionController::class, 'show']);
        Route::post('/store', [AnswerOptionController::class, 'store']);
        Route::put('/update/{id}', [AnswerOptionController::class, 'update']);
        Route::delete('/delete/{id}', [AnswerOptionController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [AnswerOptionController::class, 'toggleActive']);
        Route::get('/types', [AnswerOptionController::class, 'types']);
    });
});
