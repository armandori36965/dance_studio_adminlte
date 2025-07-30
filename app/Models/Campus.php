<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    use HasFactory;

    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'name',
        'type',
        'color',
    ];

    /**
     * 屬性轉換
     */
    protected $casts = [
        'type' => 'string',
    ];

    /**
     * 與課程類型的關聯 (一對多)
     */
    public function courseTypes(): HasMany
    {
        return $this->hasMany(CourseType::class);
    }

    /**
     * 與課程的關聯 (一對多)
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * 與校務事件的關聯 (一對多)
     */
    public function schoolEvents(): HasMany
    {
        return $this->hasMany(SchoolEvent::class);
    }

    /**
     * 獲取課程數量統計
     */
    public function getCoursesCountAttribute(): int
    {
        return $this->courses()->count();
    }

    /**
     * 獲取事件數量統計
     */
    public function getEventsCountAttribute(): int
    {
        return $this->schoolEvents()->count();
    }

    /**
     * 獲取課程類型數量統計
     */
    public function getCourseTypesCountAttribute(): int
    {
        return $this->courseTypes()->count();
    }
}