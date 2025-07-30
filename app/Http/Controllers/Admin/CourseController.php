<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseType;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    /**
     * 顯示課程列表
     */
    public function index(Request $request): View
    {
        $query = Course::with(['courseType', 'campus', 'mainTeacher', 'assistantTeachers']);

        // 篩選條件
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('course_type_id')) {
            $query->where('course_type_id', $request->course_type_id);
        }
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $courses = $query->orderBy('date', 'desc')
                        ->orderBy('start_time')
                        ->paginate(20);

        $campuses = Campus::orderBy('name')->get();
        $courseTypes = CourseType::active()->orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'campuses', 'courseTypes'));
    }

    /**
     * 顯示建立課程表單
     */
    public function create(): View
    {
        $courseTypes = CourseType::active()->with('campus')->orderBy('name')->get();
        $campuses = Campus::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.courses.create', compact('courseTypes', 'campuses', 'teachers'));
    }

    /**
     * 儲存新課程
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_type_id' => 'required|exists:course_types,id',
            'campus_id' => 'required|exists:campuses,id',
            'main_teacher_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:1000',
            'capacity' => 'required|integer|min:1|max:100',
            'assistant_teacher_ids' => 'nullable|array|max:2',
            'assistant_teacher_ids.*' => 'exists:users,id|different:main_teacher_id',
        ], [
            'course_type_id.required' => '課程類型為必填項目',
            'course_type_id.exists' => '所選課程類型不存在',
            'campus_id.required' => '上課校區為必填項目',
            'campus_id.exists' => '所選校區不存在',
            'main_teacher_id.required' => '主教老師為必填項目',
            'main_teacher_id.exists' => '所選主教老師不存在',
            'date.required' => '上課日期為必填項目',
            'date.after_or_equal' => '上課日期不能早於今天',
            'start_time.required' => '開始時間為必填項目',
            'start_time.date_format' => '開始時間格式不正確',
            'end_time.required' => '結束時間為必填項目',
            'end_time.date_format' => '結束時間格式不正確',
            'end_time.after' => '結束時間必須晚於開始時間',
            'capacity.required' => '課程容量為必填項目',
            'capacity.min' => '課程容量最少1人',
            'capacity.max' => '課程容量最多100人',
            'assistant_teacher_ids.max' => '助教老師最多2位',
            'assistant_teacher_ids.*.different' => '助教老師不能與主教老師相同',
        ]);

        $course = Course::create($validated);

        // 關聯助教老師
        if (!empty($validated['assistant_teacher_ids'])) {
            $course->assistantTeachers()->attach($validated['assistant_teacher_ids']);
        }

        return redirect()->route('admin.courses.index')
                        ->with('success', '課程建立成功！');
    }

    /**
     * 顯示編輯課程表單
     */
    public function edit(Course $course): View
    {
        $course->load(['assistantTeachers']);
        
        $courseTypes = CourseType::active()->with('campus')->orderBy('name')->get();
        $campuses = Campus::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'courseTypes', 'campuses', 'teachers'));
    }

    /**
     * 更新課程資訊
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'course_type_id' => 'required|exists:course_types,id',
            'campus_id' => 'required|exists:campuses,id',
            'main_teacher_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:1000',
            'capacity' => 'required|integer|min:1|max:100',
            'assistant_teacher_ids' => 'nullable|array|max:2',
            'assistant_teacher_ids.*' => 'exists:users,id|different:main_teacher_id',
        ], [
            'course_type_id.required' => '課程類型為必填項目',
            'course_type_id.exists' => '所選課程類型不存在',
            'campus_id.required' => '上課校區為必填項目',
            'campus_id.exists' => '所選校區不存在',
            'main_teacher_id.required' => '主教老師為必填項目',
            'main_teacher_id.exists' => '所選主教老師不存在',
            'date.required' => '上課日期為必填項目',
            'start_time.required' => '開始時間為必填項目',
            'start_time.date_format' => '開始時間格式不正確',
            'end_time.required' => '結束時間為必填項目',
            'end_time.date_format' => '結束時間格式不正確',
            'end_time.after' => '結束時間必須晚於開始時間',
            'capacity.required' => '課程容量為必填項目',
            'capacity.min' => '課程容量最少1人',
            'capacity.max' => '課程容量最多100人',
            'assistant_teacher_ids.max' => '助教老師最多2位',
            'assistant_teacher_ids.*.different' => '助教老師不能與主教老師相同',
        ]);

        $course->update($validated);

        // 更新助教老師關聯
        $course->assistantTeachers()->sync($validated['assistant_teacher_ids'] ?? []);

        return redirect()->route('admin.courses.index')
                        ->with('success', '課程更新成功！');
    }

    /**
     * 刪除課程
     */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', '課程刪除成功！');
    }

    /**
     * 顯示課程詳情
     */
    public function show(Course $course): View
    {
        $course->load(['courseType', 'campus', 'mainTeacher', 'assistantTeachers']);
        
        return view('admin.courses.show', compact('course'));
    }

    /**
     * AJAX: 根據課程類型獲取校區資訊
     */
    public function getCampusByCourseType(Request $request): JsonResponse
    {
        $courseType = CourseType::with('campus')->find($request->course_type_id);
        
        if (!$courseType) {
            return response()->json(['error' => '課程類型不存在'], 404);
        }

        return response()->json([
            'campus_id' => $courseType->campus_id,
            'campus_name' => $courseType->campus->name,
            'campus_color' => $courseType->campus->color,
        ]);
    }

    /**
     * AJAX: 根據校區獲取課程類型
     */
    public function getCourseTypesByCampus(Request $request): JsonResponse
    {
        $courseTypes = CourseType::where('campus_id', $request->campus_id)
                                ->active()
                                ->orderBy('name')
                                ->get(['id', 'name', 'category', 'level']);

        return response()->json($courseTypes);
    }
}