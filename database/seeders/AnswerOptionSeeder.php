<?php
// database/seeders/AnswerOptionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnswerOption;

class AnswerOptionSeeder extends Seeder
{
    public function run(): void
    {
        // گزینه‌های پیش‌فرض (نظر سنجی)
        $defaultOptions = [
            ['type' => 'agreement', 'value' => 1, 'label' => 'خیلی مخالف', 'icon' => '😡', 'color' => '#ef4444', 'order' => 1],
            ['type' => 'agreement', 'value' => 2, 'label' => 'مخالف', 'icon' => '😕', 'color' => '#f97316', 'order' => 2],
            ['type' => 'agreement', 'value' => 3, 'label' => 'ممتنع', 'icon' => '😐', 'color' => '#6b7280', 'order' => 3],
            ['type' => 'agreement', 'value' => 4, 'label' => 'موافق', 'icon' => '🙂', 'color' => '#3b82f6', 'order' => 4],
            ['type' => 'agreement', 'value' => 5, 'label' => 'خیلی موافق', 'icon' => '😊', 'color' => '#22c55e', 'order' => 5],
        ];

        // گزینه‌های فراوانی (چند وقت یکبار)
        $frequencyOptions = [
            ['type' => 'frequency', 'value' => 1, 'label' => 'خیلی کم', 'icon' => '🔽', 'color' => '#ef4444', 'order' => 1],
            ['type' => 'frequency', 'value' => 2, 'label' => 'کم', 'icon' => '⬇️', 'color' => '#f97316', 'order' => 2],
            ['type' => 'frequency', 'value' => 3, 'label' => 'معمولی', 'icon' => '➡️', 'color' => '#6b7280', 'order' => 3],
            ['type' => 'frequency', 'value' => 4, 'label' => 'زیاد', 'icon' => '⬆️', 'color' => '#3b82f6', 'order' => 4],
            ['type' => 'frequency', 'value' => 5, 'label' => 'خیلی زیاد', 'icon' => '🔼', 'color' => '#22c55e', 'order' => 5],
        ];

        // گزینه‌های رضایت
        $satisfactionOptions = [
            ['type' => 'satisfaction', 'value' => 1, 'label' => 'خیلی ناراضی', 'icon' => '😤', 'color' => '#ef4444', 'order' => 1],
            ['type' => 'satisfaction', 'value' => 2, 'label' => 'ناراضی', 'icon' => '😞', 'color' => '#f97316', 'order' => 2],
            ['type' => 'satisfaction', 'value' => 3, 'label' => 'معمولی', 'icon' => '😐', 'color' => '#6b7280', 'order' => 3],
            ['type' => 'satisfaction', 'value' => 4, 'label' => 'راضی', 'icon' => '🙂', 'color' => '#3b82f6', 'order' => 4],
            ['type' => 'satisfaction', 'value' => 5, 'label' => 'خیلی راضی', 'icon' => '😍', 'color' => '#22c55e', 'order' => 5],
        ];

        // گزینه‌های اهمیت
        $importanceOptions = [
            ['type' => 'importance', 'value' => 1, 'label' => 'بی‌اهمیت', 'icon' => '➖', 'color' => '#ef4444', 'order' => 1],
            ['type' => 'importance', 'value' => 2, 'label' => 'کم اهمیت', 'icon' => '🔽', 'color' => '#f97316', 'order' => 2],
            ['type' => 'importance', 'value' => 3, 'label' => 'معمولی', 'icon' => '➡️', 'color' => '#6b7280', 'order' => 3],
            ['type' => 'importance', 'value' => 4, 'label' => 'مهم', 'icon' => '⬆️', 'color' => '#3b82f6', 'order' => 4],
            ['type' => 'importance', 'value' => 5, 'label' => 'خیلی مهم', 'icon' => '⭐', 'color' => '#22c55e', 'order' => 5],
        ];

        // وارد کردن همه گزینه‌ها
        $allOptions = array_merge(
            $defaultOptions,
            $frequencyOptions,
            $satisfactionOptions,
            $importanceOptions
        );

        foreach ($allOptions as $option) {
            AnswerOption::create($option);
        }

        $this->command->info('✅ گزینه‌های پاسخ با موفقیت ایجاد شدند!');
        $this->command->info('📊 تعداد گزینه‌ها: ' . AnswerOption::count());
    }
}