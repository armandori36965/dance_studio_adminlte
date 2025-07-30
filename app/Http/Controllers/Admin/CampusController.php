<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CampusController extends Controller
{
    /**
     * 顯示校區列表
     */
    public function index(): View
    {
        $campuses = Campus::withCount(['courses', 'courseTypes', 'schoolEvents'])
                         ->orderBy('name')
                         ->get();

        return view('admin.campuses.index', compact('campuses'));
    }

    /**
     * 顯示建立校區表單
     */
    public function create(): View
    {
        return view('admin.campuses.create');
    }

    /**
     * 儲存新校區
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:campuses',
            'type' => 'required|in:school,cram_school',
            'color' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
        ], [
            'name.required' => '校區名稱為必填項目',
            'name.unique' => '校區名稱已存在',
            'type.required' => '校區類型為必填項目',
            'color.required' => '代表色為必填項目',
            'color.regex' => '代表色格式不正確，請使用 #RRGGBB 格式',
        ]);

        Campus::create($validated);

        return redirect()->route('admin.campuses.index')
                        ->with('success', '校區建立成功！');
    }

    /**
     * 顯示編輯校區表單
     */
    public function edit(Campus $campus): View
    {
        return view('admin.campuses.edit', compact('campus'));
    }

    /**
     * 更新校區資訊
     */
    public function update(Request $request, Campus $campus): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:campuses,name,' . $campus->id,
            'type' => 'required|in:school,cram_school',
            'color' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
        ], [
            'name.required' => '校區名稱為必填項目',
            'name.unique' => '校區名稱已存在',
            'type.required' => '校區類型為必填項目',
            'color.required' => '代表色為必填項目',
            'color.regex' => '代表色格式不正確，請使用 #RRGGBB 格式',
        ]);

        $campus->update($validated);

        return redirect()->route('admin.campuses.index')
                        ->with('success', '校區更新成功！');
    }

    /**
     * 刪除校區
     */
    public function destroy(Campus $campus): RedirectResponse
    {
        // 檢查是否有關聯資料
        if ($campus->courses()->exists() || $campus->courseTypes()->exists() || $campus->schoolEvents()->exists()) {
            return redirect()->route('admin.campuses.index')
                            ->with('error', '無法刪除校區，因為還有相關的課程、課程類型或校務事件。');
        }

        $campus->delete();

        return redirect()->route('admin.campuses.index')
                        ->with('success', '校區刪除成功！');
    }

    /**
     * 顯示校區詳情
     */
    public function show(Campus $campus): View
    {
        $campus->load(['courses', 'courseTypes', 'schoolEvents']);
        
        return view('admin.campuses.show', compact('campus'));
    }
}