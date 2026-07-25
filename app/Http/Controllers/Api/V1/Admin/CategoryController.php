<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('order')->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function show($id)
    {
        $category = Category::with('questions')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'slug' => 'required|string|unique:categories,slug|max:255',
    //         'description' => 'nullable|string',
    //         'order' => 'integer|default:0',
    //         'is_active' => 'boolean|default:true'
    //     ]);

    //     $category = Category::create($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'دسته‌بندی با موفقیت ایجاد شد',
    //         'data' => $category
    //     ], 201);
    // }

    // public function update(Request $request, $id)
    // {
    //     $category = Category::findOrFail($id);

    //     $validated = $request->validate([
    //         'name' => 'sometimes|string|max:255',
    //         'slug' => ['sometimes', 'string', 'max:255', Rule::unique('categories')->ignore($id)],
    //         'description' => 'nullable|string',
    //         'order' => 'integer|default:0',
    //         'is_active' => 'boolean|default:true'
    //     ]);

    //     $category->update($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'دسته‌بندی با موفقیت بروزرسانی شد',
    //         'data' => $category
    //     ]);
    // }

    public function store(Request $request)
    {
        $category = new Category();
        $category->fill($request->all());
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'دسته‌بندی با موفقیت ایجاد شد',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->fill($request->all());
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'دسته‌بندی با موفقیت بروزرسانی شد',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // بررسی وجود سوالات مرتبط
        if ($category->questions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'این دسته‌بندی دارای سوال است و قابل حذف نیست'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'دسته‌بندی با موفقیت حذف شد'
        ]);
    }

    // تغییر وضعیت فعال/غیرفعال
    public function toggleActive($id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'وضعیت دسته‌بندی تغییر کرد',
            'data' => $category
        ]);
    }
}