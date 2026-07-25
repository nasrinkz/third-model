<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Category, Question, AnswerOption};
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $questions = Question::select(['id', 'text', 'category_id', 'answer_type', 'correct_answer', 'order', 'is_active'])
        ->with('category')
        ->with(['answerOptions' => function($query) {
            $query->orderBy('order');
        }])
        ->where('is_active', true)
        ->orderBy('order')
        ->get();

        foreach($questions as $question) {
            $question->category_name = $question->category->name ?? 'بدون دسته';
        }

        $questions->makeHidden(['category']);

        $formattedQuestions = $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'text' => $question->text,
                'category_id' => $question->category_id,
                'category_name' => $question->category_name,
                'answer_type' => $question->answer_type,
                'correct_answer' => $question->correct_answer,
                'order' => $question->order,
                'is_active' => $question->is_active,
                'options' => $question->answerOptions->map(function ($option) {
                    return [
                        'value' => $option->value,
                        'label' => $option->label,
                        'icon' => $option->icon,
                        'color' => $option->color,
                    ];
                })
            ];
        });

        return response()->json([
            'questions' => $formattedQuestions,
            'total_questions' => $questions->count(),
        ]);
    }

    // دریافت لیست انواع پاسخ‌های موجود
    public function answerTypes()
    {
        $types = AnswerOption::select('type')
            ->distinct()
            ->get()
            ->pluck('type');

        return response()->json([
            'types' => $types
        ]);
    }
}