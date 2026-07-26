<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Category;
use App\Models\AnswerOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    // لیست همه سوالات به همراه دسته‌بندی و گزینه‌ها
    public function index()
    {
        $questions = Question::with(['category', 'answerOptions'])
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $questions
        ]);
    }

    public function byCategory($categoryId)
    {
        // بررسی وجود دسته‌بندی
        $category = Category::find($categoryId);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'دسته‌بندی مورد نظر یافت نشد'
            ], 404);
        }

        // دریافت سوالات آن دسته
        $questions = Question::with(['category', 'answerOptions'])
            ->where('category_id', $categoryId)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'questions' => $questions,
                'total' => $questions->count()
            ]
        ]);
    }

    // نمایش یک سوال
    public function show($id)
    {
        $question = Question::with(['category', 'answerOptions'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $question
        ]);
    }

    // ایجاد سوال جدید
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'text' => 'required|string',
            'answer_type' => 'required|string|in:agreement,frequency,satisfaction,importance',
            'correct_answer' => 'required|integer|min:1|max:5',
            'order' => 'integer|nullable',
            'is_active' => 'boolean|nullable'
        ]);

        $question = Question::create([
            'category_id' => $validated['category_id'],
            'text' => $validated['text'],
            'answer_type' => $validated['answer_type'],
            'correct_answer' => $validated['correct_answer'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'سوال با موفقیت ایجاد شد',
            'data' => $question->load(['category', 'answerOptions'])
        ], 201);
    }
  
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'text' => 'required|string',
            'answer_type' => 'required|string|in:agreement,frequency,satisfaction,importance',
            'correct_answer' => 'required|integer|min:1|max:5',
            'order' => 'integer|nullable',
            'is_active' => 'boolean|nullable'
        ]);

        $question->update([
            'category_id' => $validated['category_id'],
            'text' => $validated['text'],
            'answer_type' => $validated['answer_type'],
            'correct_answer' => $validated['correct_answer'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'سوال با موفقیت بروزرسانی شد',
            'data' => $question->load(['category', 'answerOptions'])
        ]);
    }

    // حذف سوال
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'سوال با موفقیت حذف شد'
        ]);
    }

    // تغییر وضعیت فعال/غیرفعال
    public function toggleActive($id)
    {
        $question = Question::findOrFail($id);
        $question->is_active = !$question->is_active;
        $question->save();

        return response()->json([
            'success' => true,
            'message' => 'وضعیت سوال تغییر کرد',
            'data' => $question
        ]);
    }

}