<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolEvent;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolEventController extends Controller
{
    /**
     * 顯示校務事件列表
     */
    public function index(Request $request): View
    {
        $query = SchoolEvent::with('campus');

        // 篩選條件
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $schoolEvents = $query->orderBy('start_date', 'desc')
                             ->orderBy('end_date', 'desc')
                             ->paginate(20);

        $campuses = Campus::orderBy('name')->get();

        return view('admin.school_events.index', compact('schoolEvents', 'campuses'));
    }

    /**
     * 顯示建立校務事件表單
     */
    public function create(): View
    {
        $campuses = Campus::orderBy('name')->get();
        
        return view('admin.school_events.create', compact('campuses'));
    }

    /**
     * 儲存新校務事件
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'campus_id.required' => '發生校區為必填項目',
            'campus_id.exists' => '所選校區不存在',
            'title.required' => '事件標題為必填項目',
            'start_date.required' => '開始日期為必填項目',
            'end_date.required' => '結束日期為必填項目',
            'end_date.after_or_equal' => '結束日期不能早於開始日期',
        ]);

        SchoolEvent::create($validated);

        return redirect()->route('admin.school-events.index')
                        ->with('success', '校務事件建立成功！');
    }

    /**
     * 顯示編輯校務事件表單
     */
    public function edit(SchoolEvent $schoolEvent): View
    {
        $campuses = Campus::orderBy('name')->get();
        
        return view('admin.school_events.edit', compact('schoolEvent', 'campuses'));
    }

    /**
     * 更新校務事件資訊
     */
    public function update(Request $request, SchoolEvent $schoolEvent): RedirectResponse
    {
        $validated = $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'campus_id.required' => '發生校區為必填項目',
            'campus_id.exists' => '所選校區不存在',
            'title.required' => '事件標題為必填項目',
            'start_date.required' => '開始日期為必填項目',
            'end_date.required' => '結束日期為必填項目',
            'end_date.after_or_equal' => '結束日期不能早於開始日期',
        ]);

        $schoolEvent->update($validated);

        return redirect()->route('admin.school-events.index')
                        ->with('success', '校務事件更新成功！');
    }

    /**
     * 刪除校務事件
     */
    public function destroy(SchoolEvent $schoolEvent): RedirectResponse
    {
        $schoolEvent->delete();

        return redirect()->route('admin.school-events.index')
                        ->with('success', '校務事件刪除成功！');
    }

    /**
     * 顯示校務事件詳情
     */
    public function show(SchoolEvent $schoolEvent): View
    {
        $schoolEvent->load('campus');
        
        return view('admin.school_events.show', compact('schoolEvent'));
    }

    /**
     * AJAX: 獲取行事曆事件資料
     */
    public function calendar(Request $request): \Illuminate\Http\JsonResponse
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $events = SchoolEvent::with('campus')
                            ->whereBetween('start_date', [$start, $end])
                            ->orWhereBetween('end_date', [$start, $end])
                            ->orWhere(function ($query) use ($start, $end) {
                                $query->where('start_date', '<=', $start)
                                      ->where('end_date', '>=', $end);
                            })
                            ->get()
                            ->map(function ($event) {
                                return [
                                    'id' => 'event_' . $event->id,
                                    'title' => $event->full_title,
                                    'start' => $event->start_date->format('Y-m-d'),
                                    'end' => $event->end_date->addDay()->format('Y-m-d'),
                                    'backgroundColor' => $event->campus->color,
                                    'borderColor' => $event->campus->color,
                                    'allDay' => true,
                                    'display' => 'background',
                                    'extendedProps' => [
                                        'type' => 'school_event',
                                        'campus_name' => $event->campus->name,
                                        'duration_days' => $event->duration_days,
                                    ],
                                ];
                            });

        return response()->json($events);
    }
}