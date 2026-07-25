<?php
// app/Models/AnswerOption.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnswerOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'category_id',
        'type',
        'value',
        'label',
        'description',
        'icon',
        'color',
        'order',
    ];

    protected $casts = [
        'value' => 'integer',
        'order' => 'integer',
    ];

    // رابطه با سوال
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // رابطه با دسته‌بندی
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // اسکوپ برای دریافت گزینه‌های یک نوع خاص
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // متدهای کمکی برای دریافت گزینه‌های پیش‌فرض
    public static function getDefaultOptions($type = 'agreement')
    {
        return self::where('type', $type)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    // متد برای دریافت label یک مقدار خاص
    public static function getLabel($type, $value)
    {
        $option = self::where('type', $type)
            ->where('value', $value)
            ->first();
        
        return $option ? $option->label : null;
    }

    public static function getFrequencyOptions()
    {
        return [
            ['value' => 1, 'label' => 'خیلی کم', 'icon' => '🔽', 'color' => 'red'],
            ['value' => 2, 'label' => 'کم', 'icon' => '⬇️', 'color' => 'orange'],
            ['value' => 3, 'label' => 'معمولی', 'icon' => '➡️', 'color' => 'gray'],
            ['value' => 4, 'label' => 'زیاد', 'icon' => '⬆️', 'color' => 'blue'],
            ['value' => 5, 'label' => 'خیلی زیاد', 'icon' => '🔼', 'color' => 'green'],
        ];
    }
}