<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // رابطه با سوالات
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // اسکوپ برای دسته‌بندی‌های فعال
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function activeQuestions()
    {
        return $this->hasMany(Question::class)->where('is_active', true);
    }
}