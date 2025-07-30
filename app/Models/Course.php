<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'course_type_id',
        'campus_id',
        'main_teacher_id',
        'date',
        'start_time',
        'end_time',
        'notes',
        'capacity',
        'current_enrollment',
    ];

    /**
     * 屬性轉換
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'capacity' => 'integer',
        'current_enrollment' => 'integer',
    ];

    /**
     * 與課程類型的關聯 (多對一)
     */
    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }

    /**
     * 與校區的關聯 (多對一)
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * 與主教老師的關聯 (多對一)
     */
    public function mainTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_teacher_id');
    }

    /**
     * 與助教老師的關聯 (多對多)
     */
    public function assistantTeachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_assistant_teachers', 'course_id', 'teacher_id')
                    ->withTimestamps();
    }

    /**
     * 獲取剩餘名額
     */
    public function getRemainingSeatsAttribute(): int
    {
        return $this->capacity - $this->current_enrollment;
    }

    /**
     * 檢查是否還有名額
     */
    public function getHasAvailableSeatsAttribute(): bool
    {
        return $this->remaining_seats > 0;
    }

    /**
     * 獲取課程完整標題
     */
    public function getFullTitleAttribute(): string
    {
        return "[{$this->campus->name}] {$this->courseType->name}";
    }

    /**
     * 範圍查詢：未來的課程
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * 範圍查詢：過去的課程
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString());
    }

    /**
     * 範圍查詢：今天的課程
     */
    public function scopeToday($query)
    {
        return $query->where('date', now()->toDateString());
    }
}
