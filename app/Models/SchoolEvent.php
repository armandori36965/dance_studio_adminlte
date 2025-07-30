<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolEvent extends Model
{
    use HasFactory;

    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'campus_id',
        'title',
        'start_date',
        'end_date',
    ];

    /**
     * 屬性轉換
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * 與校區的關聯 (多對一)
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * 獲取事件完整標題
     */
    public function getFullTitleAttribute(): string
    {
        return "[{$this->campus->name}] {$this->title}";
    }

    /**
     * 獲取事件持續天數
     */
    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * 檢查是否為單日事件
     */
    public function getIsSingleDayAttribute(): bool
    {
        return $this->start_date->equalTo($this->end_date);
    }

    /**
     * 檢查是否為多日事件
     */
    public function getIsMultiDayAttribute(): bool
    {
        return !$this->is_single_day;
    }

    /**
     * 範圍查詢：未來的事件
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    /**
     * 範圍查詢：過去的事件
     */
    public function scopePast($query)
    {
        return $query->where('end_date', '<', now()->toDateString());
    }

    /**
     * 範圍查詢：當前進行中的事件
     */
    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now()->toDateString())
                     ->where('end_date', '>=', now()->toDateString());
    }
}
