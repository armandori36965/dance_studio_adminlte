<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_type_id')->constrained()->onDelete('cascade'); // 課程類型
            $table->foreignId('campus_id')->constrained()->onDelete('cascade'); // 上課校區
            $table->foreignId('main_teacher_id')->constrained('users')->onDelete('cascade'); // 主教老師
            $table->date('date'); // 上課日期
            $table->time('start_time'); // 開始時間
            $table->time('end_time'); // 結束時間
            $table->text('notes')->nullable(); // 備註
            $table->integer('capacity')->default(20); // 課程容量
            $table->integer('current_enrollment')->default(0); // 當前報名人數
            $table->timestamps();
        });

        // 課程與助教老師的多對多關聯表
        Schema::create('course_assistant_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_assistant_teachers');
        Schema::dropIfExists('courses');
    }
};
