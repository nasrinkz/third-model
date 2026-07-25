<?php
// app/Models/Question.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'text',
        'answer_type',
        'correct_answer',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'correct_answer' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // رابطه با گزینه‌های پاسخ از طریق answer_type
    public function answerOptions()
    {
        return $this->hasMany(AnswerOption::class, 'type', 'answer_type')
            ->orderBy('order');
    }

    // اسکوپ برای سوالات فعال
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // متد کمکی برای دریافت گزینه‌ها
    public function getOptionsAttribute()
    {
        return $this->answerOptions;
    }
}