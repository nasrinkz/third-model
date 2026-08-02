<?php
// app/Http/Controllers/Admin/AnswerOptionController.php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnswerOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AnswerOptionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $type = $request->get('type');
            
            $query = AnswerOption::select('id', 'type', 'value', 'label', 'icon', 'color', 'order', 'is_active')
                ->orderBy('type')
                ->orderBy('order');
            
            if ($type) {
                $query->where('type', $type);
            }
            
            $options = $query->get();

            return response()->json([
                'success' => true,
                'data' => $options,
                'total' => $options->count()
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
            $option = AnswerOption::select('id', 'type', 'value', 'label', 'icon', 'color', 'order', 'is_active')
                ->findOrFail($id);
                
            return response()->json([
                'success' => true,
                'data' => $option
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'گزینه مورد نظر یافت نشد'
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
                'type' => 'required|string|max:255',
                'value' => 'required|integer|min:1|max:5',
                'label' => 'required|string|max:255',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:50',
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

            // بررسی یکتا بودن type + value
            $exists = AnswerOption::where('type', $validated['type'])
                ->where('value', $validated['value'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'این ترکیب نوع و مقدار قبلاً ثبت شده است'
                ], 400);
            }

            $option = AnswerOption::create([
                'type' => $validated['type'],
                'value' => $validated['value'],
                'label' => $validated['label'],
                'icon' => $validated['icon'] ?? null,
                'color' => $validated['color'] ?? null,
                'order' => $validated['order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'گزینه با موفقیت ایجاد شد',
                'data' => $option
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
            $option = AnswerOption::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|string|max:255',
                'value' => 'sometimes|integer|min:1|max:5',
                'label' => 'sometimes|string|max:255',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:50',
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

            // بررسی یکتا بودن در صورت تغییر type یا value
            if (isset($validated['type']) || isset($validated['value'])) {
                $newType = $validated['type'] ?? $option->type;
                $newValue = $validated['value'] ?? $option->value;
                
                $exists = AnswerOption::where('type', $newType)
                    ->where('value', $newValue)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'این ترکیب نوع و مقدار قبلاً ثبت شده است'
                    ], 400);
                }
            }

            $option->update([
                'type' => $validated['type'] ?? $option->type,
                'value' => $validated['value'] ?? $option->value,
                'label' => $validated['label'] ?? $option->label,
                'icon' => $validated['icon'] ?? $option->icon,
                'color' => $validated['color'] ?? $option->color,
                'order' => $validated['order'] ?? $option->order,
                'is_active' => $validated['is_active'] ?? $option->is_active
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'گزینه با موفقیت بروزرسانی شد',
                'data' => $option->fresh()
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'گزینه مورد نظر یافت نشد'
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
            $option = AnswerOption::findOrFail($id);
            $option->delete();

            return response()->json([
                'success' => true,
                'message' => 'گزینه با موفقیت حذف شد'
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'گزینه مورد نظر یافت نشد'
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
            $option = AnswerOption::findOrFail($id);
            $option->is_active = !$option->is_active;
            $option->save();

            return response()->json([
                'success' => true,
                'message' => $option->is_active ? 'گزینه فعال شد' : 'گزینه غیرفعال شد',
                'data' => $option
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'گزینه مورد نظر یافت نشد'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    public function types()
    {
        try {
            $types = AnswerOption::select('type')
                ->distinct()
                ->get()
                ->pluck('type')
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $types,
                'total' => count($types)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    public function getByType($type)
    {
        try {
            $options = AnswerOption::where('type', $type)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            if ($options->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'هیچ گزینه‌ای برای این نوع پیدا نشد'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $options,
                'total' => $options->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.'
            ], 500);
        }
    }

    /**
     * حذف گروهی گزینه‌ها بر اساس نوع
     */
    public function deleteByType($type)
    {
        try {
            $deleted = AnswerOption::where('type', $type)->delete();

            if ($deleted === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'هیچ گزینه‌ای برای این نوع پیدا نشد'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => "{$deleted} گزینه با موفقیت حذف شد",
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
