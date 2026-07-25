<?php
// database/seeders/QuestionSeeder.php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        // 1. ایجاد دسته‌بندی‌ها
        $categories = [
            ['name' => 'اقتصاد', 'slug' => 'economy', 'order' => 1],
            ['name' => 'سیاست', 'slug' => 'politics', 'order' => 2],
            ['name' => 'فرهنگ', 'slug' => 'culture', 'order' => 3],
            ['name' => 'عدالت', 'slug' => 'justice', 'order' => 4],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // 2. ایجاد سوالات (مثال)
        $economy = Category::where('slug', 'economy')->first();
        $politics = Category::where('slug', 'politics')->first();
        $culture = Category::where('slug', 'culture')->first();
        $justice = Category::where('slug', 'justice')->first();

        $questions = [
            // اقتصاد - نوع agreement (موافقت)
            [
                'category_id' => $economy->id,
                'text' => 'اقتصاد کشور باید متکی بر تولید داخلی و خودکفایی باشد',
                'answer_type' => 'agreement', // ← اضافه شده
                'correct_answer' => 5,
                'order' => 1,
                'is_active' => true
            ],
            [
                'category_id' => $economy->id,
                'text' => 'خصوصی‌سازی باید با سرعت بیشتری در کشور اجرا شود',
                'answer_type' => 'agreement',
                'correct_answer' => 4,
                'order' => 2,
                'is_active' => true
            ],
            [
                'category_id' => $economy->id,
                'text' => 'چقدر به عملکرد اقتصادی دولت اعتماد دارید؟',
                'answer_type' => 'satisfaction', // ← نوع رضایت
                'correct_answer' => 4,
                'order' => 3,
                'is_active' => true
            ],
            
            // سیاست - نوع agreement
            [
                'category_id' => $politics->id,
                'text' => 'اولویت سیاست خارجی ایران باید تعامل با کشورهای همسایه باشد',
                'answer_type' => 'agreement',
                'correct_answer' => 5,
                'order' => 1,
                'is_active' => true
            ],
            [
                'category_id' => $politics->id,
                'text' => 'مذاکره با آمریکا می‌تواند بسیاری از مشکلات کشور را حل کند',
                'answer_type' => 'agreement',
                'correct_answer' => 1,
                'order' => 2,
                'is_active' => true
            ],
            
            // فرهنگ - نوع agreement
            [
                'category_id' => $culture->id,
                'text' => 'حجاب باید در جامعه به صورت قانونی الزامی باشد',
                'answer_type' => 'agreement',
                'correct_answer' => 4,
                'order' => 1,
                'is_active' => true
            ],
            [
                'category_id' => $culture->id,
                'text' => 'چقدر به برنامه‌های فرهنگی صداوسیما علاقه دارید؟',
                'answer_type' => 'frequency', // ← نوع فراوانی
                'correct_answer' => 3,
                'order' => 2,
                'is_active' => true
            ],
            
            // عدالت - نوع agreement
            [
                'category_id' => $justice->id,
                'text' => 'ثروت و امکانات باید به صورت عادلانه در جامعه توزیع شود',
                'answer_type' => 'agreement',
                'correct_answer' => 5,
                'order' => 1,
                'is_active' => true
            ],
            
            // علم و فناوری - نوع importance (اهمیت)
            [
                'category_id' => $politics->id,
                'text' => 'توسعه فناوری‌های نوین چه اهمیتی برای کشور دارد؟',
                'answer_type' => 'importance', // ← نوع اهمیت
                'correct_answer' => 5,
                'order' => 1,
                'is_active' => true
            ],
            [
                'category_id' => $politics->id,
                'text' => 'چقدر به پیشرفت علمی کشور در ۱۰ سال اخیر افتخار می‌کنید؟',
                'answer_type' => 'satisfaction',
                'correct_answer' => 4,
                'order' => 2,
                'is_active' => true
            ],
        ];

        foreach ($questions as $q) {
            Question::create($q);
        }
    }
}