<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseTypeController extends Controller
{
    /**
     * 顯示課程類型列表
     */
    public function index(): View
    {
        $courseTypes = CourseType::with(['campus'])
                                ->withCount('courses')
                                ->orderBy('name')
                                ->get();

        return view('admin.course_types.index', compact('courseTypes'));
    }

    /**
     * 顯示建立課程類型表單
     */
    public function create(): View
    {
        $campuses = Campus::orderBy('name')->get();
        
        return view('admin.course_types.create', compact('campuses'));
    }

    /**
     * 儲存新課程類型
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'campus_id' => 'required|exists:campuses,id',
            'category' => 'required|in:general,competition',
            'level' => 'nullable|string|max:255',
            'duration' => 'required|integer|min:15|max:480',
            'price' => 'required|numeric|min:0|max:999999.99',
            'max_students' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => '課程類型名稱為必填項目',
            'campus_id.required' => '所屬校區為必填項目',
            'campus_id.exists' => '所選校區不存在',
            'category.required' => '分類為必填項目',
            'duration.required' => '課程時長為必填項目',
            'duration.min' => '課程時長最少15分鐘',
            'duration.max' => '課程時長最多480分鐘',
            'price.required' => '課程價格為必填項目',
            'price.min' => '課程價格不能為負數',
            'max_students.required' => '最大學生數為必填項目',
            'max_students.min' => '最大學生數最少1人',
            'max_students.max' => '最大學生數最多100人',
            'status.required' => '狀態為必填項目',
        ]);

        CourseType::create($validated);

        return redirect()->route('admin.course-types.index')
                        ->with('success', '課程類型建立成功！');
    }

    /**
     * 顯示編輯課程類型表單
     */
    public function edit(CourseType $courseType): View
    {
        $campuses = Campus::orderBy('name')->get();
        
        return view('admin.course_types.edit', compact('courseType', 'campuses'));
    }

    /**
     * 更新課程類型資訊
     */
    public function update(Request $request, CourseType $courseType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'campus_id' => 'required|exists:campuses,id',
            'category' => 'required|in:general,competition',
            'level' => 'nullable|string|max:255',
            'duration' => 'required|integer|min:15|max:480',
            'price' => 'required|numeric|min:0|max:999999.99',
            'max_students' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => '課程類型名稱為必填項目',
            'campus_id.required' => '所屬校區為必填項目',
            'campus_id.exists' => '所選校區不存在',
            'category.required' => '分類為必填項目',
            'duration.required' => '課程時長為必填項目',
            'duration.min' => '課程時長最少15分鐘',
            'duration.max' => '課程時長最多480分鐘',
            'price.required' => '課程價格為必填項目',
            'price.min' => '課程價格不能為負數',
            'max_students.required' => '最大學生數為必填項目',
            'max_students.min' => '最大學生數最少1人',
            'max_students.max' => '最大學生數最多100人',
            'status.required' => '狀態為必填項目',
        ]);

        $courseType->update($validated);

        return redirect()->route('admin.course-types.index')
                        ->with('success', '課程類型更新成功！');
    }

    /**
     * 刪除課程類型
     */
    public function destroy(CourseType $courseType): RedirectResponse
    {
        // 檢查是否有關聯課程
        if ($courseType->courses()->exists()) {
            return redirect()->route('admin.course-types.index')
                            ->with('error', '無法刪除課程類型，因為還有相關的課程。');
        }

        $courseType->delete();

        return redirect()->route('admin.course-types.index')
                        ->with('success', '課程類型刪除成功！');
    }

    /**
     * 顯示課程類型詳情
     */
    public function show(CourseType $courseType): View
    {
        $courseType->load(['campus', 'courses']);
        
        return view('admin.course_types.show', compact('courseType'));
    }

    /**
     * 複製課程類型
     */
    public function duplicate(CourseType $courseType): RedirectResponse
    {
        $newCourseType = $courseType->replicate();
        $newCourseType->name = $courseType->name . ' (複製)';
        $newCourseType->status = 'inactive';
        $newCourseType->save();

        return redirect()->route('admin.course-types.edit', $newCourseType)
                        ->with('success', '課程類型複製成功！請編輯新建立的課程類型。');
    }

    /**
     * 批量更新狀態
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_type_ids' => 'required|array',
            'course_type_ids.*' => 'exists:course_types,id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        $courseTypes = CourseType::whereIn('id', $validated['course_type_ids'])->get();

        switch ($validated['action']) {
            case 'activate':
                $courseTypes->each(function ($courseType) {
                    $courseType->update(['status' => 'active']);
                });
                $message = '選中的課程類型已啟用！';
                break;
            case 'deactivate':
                $courseTypes->each(function ($courseType) {
                    $courseType->update(['status' => 'inactive']);
                });
                $message = '選中的課程類型已停用！';
                break;
            case 'delete':
                // 檢查是否有關聯課程
                foreach ($courseTypes as $courseType) {
                    if ($courseType->courses()->exists()) {
                        return redirect()->route('admin.course-types.index')
                                        ->with('error', "無法刪除課程類型「{$courseType->name}」，因為還有相關的課程。");
                    }
                }
                $courseTypes->each(function ($courseType) {
                    $courseType->delete();
                });
                $message = '選中的課程類型已刪除！';
                break;
        }

        return redirect()->route('admin.course-types.index')
                        ->with('success', $message);
    }
}