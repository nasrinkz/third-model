<?php
// app/Http/Controllers/Admin/AnswerOptionController.php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnswerOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnswerOptionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');
        
        $query = AnswerOption::orderBy('type')->orderBy('order');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $options = $query->get();

        return response()->json([
            'success' => true,
            'data' => $options
        ]);
    }

    public function show($id)
    {
        $option = AnswerOption::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $option
        ]);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'type' => 'required|string|max:255',
    //         'value' => 'required|integer|min:1|max:5',
    //         'label' => 'required|string|max:255',
    //         'icon' => 'nullable|string|max:50',
    //         'color' => 'nullable|string|max:50',
    //         'order' => 'integer|default:0',
    //         'is_active' => 'boolean|default:true'
    //     ]);

    //     // بررسی یکتا بودن type + value
    //     $exists = AnswerOption::where('type', $validated['type'])
    //         ->where('value', $validated['value'])
    //         ->exists();

    //     if ($exists) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'این ترکیب نوع و مقدار قبلاً ثبت شده است'
    //         ], 400);
    //     }

    //     $option = AnswerOption::create($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'گزینه با موفقیت ایجاد شد',
    //         'data' => $option
    //     ], 201);
    // }

    // public function update(Request $request, $id)
    // {
    //     $option = AnswerOption::findOrFail($id);

    //     $validated = $request->validate([
    //         'type' => 'sometimes|string|max:255',
    //         'value' => 'sometimes|integer|min:1|max:5',
    //         'label' => 'sometimes|string|max:255',
    //         'icon' => 'nullable|string|max:50',
    //         'color' => 'nullable|string|max:50',
    //         'order' => 'integer|default:0',
    //         'is_active' => 'boolean|default:true'
    //     ]);

    //     // بررسی یکتا بودن در صورت تغییر type یا value
    //     if (isset($validated['type']) || isset($validated['value'])) {
    //         $newType = $validated['type'] ?? $option->type;
    //         $newValue = $validated['value'] ?? $option->value;
            
    //         $exists = AnswerOption::where('type', $newType)
    //             ->where('value', $newValue)
    //             ->where('id', '!=', $id)
    //             ->exists();

    //         if ($exists) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'این ترکیب نوع و مقدار قبلاً ثبت شده است'
    //             ], 400);
    //         }
    //     }

    //     $option->update($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'گزینه با موفقیت بروزرسانی شد',
    //         'data' => $option
    //     ]);
    // }

    public function store(Request $request)
    {
        try {
            $option = AnswerOption::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'گزینه با موفقیت ایجاد شد',
                'data' => $option
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ایجاد گزینه: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $option = AnswerOption::findOrFail($id);
            $option->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'گزینه با موفقیت بروزرسانی شد',
                'data' => $option
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در بروزرسانی گزینه: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $option = AnswerOption::findOrFail($id);
        $option->delete();

        return response()->json([
            'success' => true,
            'message' => 'گزینه با موفقیت حذف شد'
        ]);
    }

    public function toggleActive($id)
    {
        $option = AnswerOption::findOrFail($id);
        $option->is_active = !$option->is_active;
        $option->save();

        return response()->json([
            'success' => true,
            'message' => 'وضعیت گزینه تغییر کرد',
            'data' => $option
        ]);
    }

    // دریافت انواع گزینه‌ها (برای select box)
    public function types()
    {
        $types = AnswerOption::select('type')
            ->distinct()
            ->get()
            ->pluck('type');

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }
}