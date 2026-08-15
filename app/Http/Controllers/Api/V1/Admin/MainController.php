<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Question;
use App\Models\AnswerOption;

class MainController extends Controller
{
    public function stats()
    {
        try {
            // آمار دسته‌بندی‌ها
            $categoriesStats = [
                'total' => Category::count(),
                'active' => Category::where('is_active', true)->count(),
                'inactive' => Category::where('is_active', false)->count(),
            ];

            // آمار سوالات
            $questionsStats = [
                'total' => Question::count(),
                'active' => Question::where('is_active', true)->count(),
                'inactive' => Question::where('is_active', false)->count(),
            ];

            // آمار گزینه‌های پاسخ
            $answerOptionsStats = [
                'total' => AnswerOption::count(),
                'active' => AnswerOption::where('is_active', true)->count(),
                'inactive' => AnswerOption::where('is_active', false)->count(),
            ];

            $latestQuestions = Question::with(['category', 'answerOptions'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($question) {
                // دریافت نوع پاسخ به فارسی
                $answerTypeLabels = [
                    'agreement' => 'موافقت',
                    'frequency' => 'فراوانی',
                    'satisfaction' => 'رضایت',
                    'importance' => 'اهمیت',
                ];

                return [
                    'id' => $question->id,
                    'text' => $question->text,
                    'category_name' => $question->category->name ?? 'بدون دسته',
                    'category_id' => $question->category_id,
                    'answer_type' => $question->answer_type,
                    'answer_type_label' => $answerTypeLabels[$question->answer_type] ?? $question->answer_type,
                    'is_active' => $question->is_active,
                    'created_at' => $question->created_at,
                    'created_at_diff' => $question->created_at->diffForHumans(),
                ];
            });


            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categoriesStats,
                    'questions' => $questionsStats,
                    'answer_options' => $answerOptionsStats,
                    'latest_questions' => $latestQuestions,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت آمار: ' . $e->getMessage()
            ], 500);
        }
    }
}
