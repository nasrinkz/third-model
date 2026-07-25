<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answer_options', function (Blueprint $table) {
            $table->id();
            
            $table->string('type')->default('default'); 
            // 'default' => خیلی مخالف تا خیلی موافق
            // 'frequency' => خیلی زیاد تا خیلی کم
            // 'agreement' => کاملاً موافق تا کاملاً مخالف
            // 'satisfaction' => خیلی راضی تا خیلی ناراضی
            
            // مقدار گزینه (1 تا 5)
            $table->tinyInteger('value'); // 1, 2, 3, 4, 5
            
            // متن نمایشی گزینه
            $table->string('label'); // "خیلی مخالف"، "خیلی زیاد"، ...
            
            // آیکون یا ایموجی (اختیاری)
            $table->string('icon')->nullable(); // "😡", "😊", ...
            
            // رنگ (برای نمایش در UI)
            $table->string('color')->nullable(); // "red", "green", ...
            
            // ترتیب نمایش
            $table->integer('order')->default(0);
            
            $table->timestamps();
            
            // هر نوع، فقط یک گزینه با هر مقدار می‌تواند داشته باشد
            $table->unique(['type', 'value']);
            
            // ایندکس برای جستجوی سریع
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_options');
    }
};