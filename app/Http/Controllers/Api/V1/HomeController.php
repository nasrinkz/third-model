<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AnswerOption;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    public function index()
    {
        return $this->questionsResponse(Question::whereHas('category', fn ($query) => $query->where('is_active', true)));
    }

    public function byCategory(string $category)
    {
        $categoryModel = Category::where('slug', $category)->where('is_active', true)->firstOrFail();

        return $this->questionsResponse(Question::where('category_id', $categoryModel->id));
    }

    public function answerTypes()
    {
        return response()->json(['types' => AnswerOption::distinct()->pluck('type')]);
    }

    private function questionsResponse(Builder $query)
    {
        $questions = $query->select(['id', 'text', 'category_id', 'answer_type', 'order'])
            ->with('category:id,name')
            ->with(['answerOptions' => fn ($options) => $options->where('is_active', true)->orderBy('order')])
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'questions' => $questions->map(fn ($question) => [
                'id' => $question->id,
                'text' => $question->text,
                'category_id' => $question->category_id,
                'category_name' => $question->category?->name,
                'answer_type' => $question->answer_type,
                'order' => $question->order,
                'options' => $question->answerOptions->map(fn ($option) => [
                    'value' => $option->value,
                    'label' => $option->label,
                    'icon' => $option->icon,
                    'color' => $option->color,
                ]),
            ]),
            'total_questions' => $questions->count(),
        ]);
    }
}
