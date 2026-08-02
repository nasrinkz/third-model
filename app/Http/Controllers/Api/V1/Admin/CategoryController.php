<?php
// app/Http/Controllers/Api/V1/Admin/CategoryController.php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::orderBy('order')
                ->orderBy('id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'total' => $categories->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::with(['questions' => function($query) {
                $query->orderBy('order');
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'دسته‌بندی مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:categories,slug|max:255',
                'description' => 'nullable|string',
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

            $category = Category::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'order' => $validated['order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'دسته‌بندی با موفقیت ایجاد شد',
                'data' => $category
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'slug' => ['sometimes', 'string', 'max:255', Rule::unique('categories')->ignore($id)],
                'description' => 'nullable|string',
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

            // اگر داده‌ای برای بروزرسانی ارسال نشده باشد
            if (empty($validated)) {
                return response()->json([
                    'success' => false,
                    'message' => 'هیچ داده‌ای برای بروزرسانی ارسال نشده است'
                ], 400);
            }

            $category->update([
                'name' => $validated['name'] ?? $category->name,
                'slug' => $validated['slug'] ?? $category->slug,
                'description' => $validated['description'] ?? $category->description,
                'order' => $validated['order'] ?? $category->order,
                'is_active' => $validated['is_active'] ?? $category->is_active
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'دسته‌بندی با موفقیت بروزرسانی شد',
                'data' => $category->fresh()
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'دسته‌بندی مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // بررسی وجود سوالات مرتبط
            if ($category->questions()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'این دسته‌بندی دارای ' . $category->questions()->count() . ' سوال است و قابل حذف نیست',
                    'related_questions_count' => $category->questions()->count()
                ], 400);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'دسته‌بندی با موفقیت حذف شد'
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'دسته‌بندی مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    public function toggleActive($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->is_active = !$category->is_active;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => $category->is_active ? 'دسته‌بندی فعال شد' : 'دسته‌بندی غیرفعال شد',
                'data' => $category
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'دسته‌بندی مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    /**
     * دریافت دسته‌بندی به همراه سوالات آن
     */
    public function getWithQuestions($id)
    {
        try {
            $category = Category::with(['questions' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('order');
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'questions_count' => $category->questions->count()
                ]
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'دسته‌بندی مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    /**
     * تغییر ترتیب دسته‌بندی‌ها
     */
    public function reorder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:categories,id',
                'orders.*.order' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در اعتبارسنجی داده‌ها',
                    'errors' => $validator->errors()
                ], 422);
            }

            foreach ($request->orders as $item) {
                Category::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'ترتیب دسته‌بندی‌ها با موفقیت تغییر کرد'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    /**
     * حذف چند دسته‌بندی به صورت گروهی
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'required|exists:categories,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در اعتبارسنجی داده‌ها',
                    'errors' => $validator->errors()
                ], 422);
            }

            // بررسی وجود سوالات مرتبط
            $categories = Category::withCount('questions')
                ->whereIn('id', $request->ids)
                ->get();

            $hasQuestions = $categories->filter(function($cat) {
                return $cat->questions_count > 0;
            });

            if ($hasQuestions->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'برخی دسته‌بندی‌ها دارای سوال هستند و قابل حذف نیستند',
                    'categories_with_questions' => $hasQuestions->pluck('name')
                ], 400);
            }

            $deleted = Category::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deleted} دسته‌بندی با موفقیت حذف شد",
                'deleted_count' => $deleted
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }
}
