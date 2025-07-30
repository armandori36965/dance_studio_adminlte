<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseType extends Model
{
    use HasFactory;

    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'name',
        'campus_id',
        'category',
        'level',
        'duration',
        'price',
        'max_students',
        'status',
    ];

    /**
     * 屬性轉換
     */
    protected $casts = [
        'category' => 'string',
        'duration' => 'integer',
        'price' => 'decimal:2',
        'max_students' => 'integer',
        'status' => 'string',
    ];

    /**
     * 與校區的關聯 (多對一)
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * 與課程的關聯 (一對多)
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * 獲取課程數量統計
     */
    public function getCoursesCountAttribute(): int
    {
        return $this->courses()->count();
    }

    /**
     * 獲取活躍課程數量統計
     */
    public function getActiveCoursesCountAttribute(): int
    {
        return $this->courses()->where('date', '>=', now()->toDateString())->count();
    }

    /**
     * 範圍查詢：活躍的課程類型
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * 範圍查詢：一般課程
     */
    public function scopeGeneral($query)
    {
        return $query->where('category', 'general');
    }

    /**
     * 範圍查詢：比賽隊課程
     */
    public function scopeCompetition($query)
    {
        return $query->where('category', 'competition');
    }
}