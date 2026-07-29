<?php
// app/Http/Controllers/Api/V1/Admin/QuestionController.php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Question, Category, AnswerOption};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Question::with(['category', 'answerOptions']);

            // فیلتر بر اساس دسته‌بندی
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // فیلتر بر اساس دسته‌بندی (با slug)
            if ($request->has('category_slug') && $request->category_slug) {
                $category = Category::where('slug', $request->category_slug)->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }

            // فیلتر بر اساس نوع پاسخ
            if ($request->has('answer_type') && $request->answer_type) {
                $query->where('answer_type', $request->answer_type);
            }

            // فیلتر بر اساس وضعیت فعال/غیرفعال
            if ($request->has('is_active') && $request->is_active !== null) {
                $query->where('is_active', $request->is_active);
            }

            // جستجو در متن سوال
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('text', 'like', "%{$search}%")
                      ->orWhereHas('category', function($cat) use ($search) {
                          $cat->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // مرتب‌سازی
            $sortBy = $request->get('sort_by', 'order');
            $sortOrder = $request->get('sort_order', 'asc');
            
            // اعتبارسنجی فیلدهای مرتب‌سازی
            $allowedSortFields = ['id', 'category_id', 'text', 'answer_type', 'correct_answer', 'order', 'is_active', 'created_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('order', 'asc');
            }

            $perPage = $request->get('per_page', 15);
            $perPage = min($perPage, 100); // حداکثر 100 آیتم در هر صفحه
            
            $questions = $query->paginate($perPage);

            // اضافه کردن اطلاعات فیلترها به پاسخ
            return response()->json([
                'success' => true,
                'data' => $questions->items(),
                'pagination' => [
                    'current_page' => $questions->currentPage(),
                    'last_page' => $questions->lastPage(),
                    'per_page' => $questions->perPage(),
                    'total' => $questions->total(),
                    'from' => $questions->firstItem(),
                    'to' => $questions->lastItem(),
                    'next_page_url' => $questions->nextPageUrl(),
                    'prev_page_url' => $questions->previousPageUrl(),
                    'links' => $questions->linkCollection()->toArray()
                ],
                'filters' => [
                    'category_id' => $request->category_id,
                    'category_slug' => $request->category_slug,
                    'answer_type' => $request->answer_type,
                    'is_active' => $request->is_active,
                    'search' => $request->search,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder
                ],
                'total_filtered' => $query->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت لیست سوالات: ' . $e->getMessage()
            ], 500);
        }
    }

    public function byCategory(Request $request, $categoryId)
    {
        try {
            $category = Category::find($categoryId);
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'دسته‌بندی مورد نظر یافت نشد'
                ], 404);
            }

            $query = Question::with(['category', 'answerOptions'])
                ->where('category_id', $categoryId);

            // فیلتر بر اساس وضعیت فعال
            if ($request->has('is_active') && $request->is_active !== null) {
                $query->where('is_active', $request->is_active);
            }

            // جستجو در متن سوال
            if ($request->has('search') && $request->search) {
                $query->where('text', 'like', "%{$request->search}%");
            }

            $perPage = $request->get('per_page', 15);
            $questions = $query->orderBy('order')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'questions' => $questions->items(),
                    'pagination' => [
                        'current_page' => $questions->currentPage(),
                        'last_page' => $questions->lastPage(),
                        'per_page' => $questions->perPage(),
                        'total' => $questions->total()
                    ],
                    'total' => $questions->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت سوالات دسته‌بندی: ' . $e->getMessage()
            ], 500);
        }
    }

    public function byCategorySlug(Request $request, $slug)
    {
        try {
            $category = Category::where('slug', $slug)->first();
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'دسته‌بندی مورد نظر یافت نشد'
                ], 404);
            }

            $query = Question::with(['category', 'answerOptions'])
                ->where('category_id', $category->id)
                ->where('is_active', true);

            // جستجو در متن سوال
            if ($request->has('search') && $request->search) {
                $query->where('text', 'like', "%{$request->search}%");
            }

            $perPage = $request->get('per_page', 15);
            $questions = $query->orderBy('order')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'questions' => $questions->items(),
                    'pagination' => [
                        'current_page' => $questions->currentPage(),
                        'last_page' => $questions->lastPage(),
                        'per_page' => $questions->perPage(),
                        'total' => $questions->total()
                    ],
                    'total' => $questions->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت سوالات: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $question = Question::with(['category', 'answerOptions'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $question
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'سوال مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در نمایش سوال: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
                'text' => 'required|string|max:1000',
                'answer_type' => 'required|string|in:agreement,frequency,satisfaction,importance',
                'correct_answer' => 'required|integer|min:1|max:5',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در اعتبارسنجی داده‌ها',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

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
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ایجاد سوال: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $question = Question::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'category_id' => 'sometimes|exists:categories,id',
                'text' => 'sometimes|string|max:1000',
                'answer_type' => 'sometimes|string|in:agreement,frequency,satisfaction,importance',
                'correct_answer' => 'sometimes|integer|min:1|max:5',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در اعتبارسنجی داده‌ها',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            if (empty($validated)) {
                return response()->json([
                    'success' => false,
                    'message' => 'هیچ داده‌ای برای بروزرسانی ارسال نشده است'
                ], 400);
            }

            $question->update([
                'category_id' => $validated['category_id'] ?? $question->category_id,
                'text' => $validated['text'] ?? $question->text,
                'answer_type' => $validated['answer_type'] ?? $question->answer_type,
                'correct_answer' => $validated['correct_answer'] ?? $question->correct_answer,
                'order' => $validated['order'] ?? $question->order,
                'is_active' => $validated['is_active'] ?? $question->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'سوال با موفقیت بروزرسانی شد',
                'data' => $question->load(['category', 'answerOptions'])
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'سوال مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در بروزرسانی سوال: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id);
            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'سوال با موفقیت حذف شد'
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'سوال مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف سوال: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleActive($id)
    {
        try {
            $question = Question::findOrFail($id);
            $question->is_active = !$question->is_active;
            $question->save();

            return response()->json([
                'success' => true,
                'message' => $question->is_active ? 'سوال فعال شد' : 'سوال غیرفعال شد',
                'data' => $question
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'سوال مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در تغییر وضعیت سوال: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * دریافت آمار سوالات
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Question::count(),
                'active' => Question::where('is_active', true)->count(),
                'inactive' => Question::where('is_active', false)->count(),
                'by_category' => Question::with('category')
                    ->select('category_id', \DB::raw('count(*) as total'))
                    ->groupBy('category_id')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'category_id' => $item->category_id,
                            'category_name' => $item->category->name ?? 'بدون دسته',
                            'total' => $item->total
                        ];
                    }),
                'by_answer_type' => Question::select('answer_type', \DB::raw('count(*) as total'))
                    ->groupBy('answer_type')
                    ->get()
                    ->pluck('total', 'answer_type')
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت آمار: ' . $e->getMessage()
            ], 500);
        }
    }
}