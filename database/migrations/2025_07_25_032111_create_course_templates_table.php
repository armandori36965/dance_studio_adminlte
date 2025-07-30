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
        Schema::create('course_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 課程類型名稱
            $table->foreignId('campus_id')->constrained()->onDelete('cascade'); // 所屬校區
            $table->enum('category', ['general', 'competition'])->default('general'); // 分類
            $table->string('level')->nullable(); // 程度等級
            $table->integer('duration')->default(60); // 課程時長 (分鐘)
            $table->decimal('price', 8, 2)->default(0); // 課程價格
            $table->integer('max_students')->default(20); // 最大學生數
            $table->enum('status', ['active', 'inactive'])->default('active'); // 狀態
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_types');
    }
};
