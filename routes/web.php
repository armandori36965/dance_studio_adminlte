<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\Admin\CourseTypeController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\SchoolEventController;

Route::get('/', function () {
    return view('welcome');
});

// 管理員路由群組
Route::prefix('admin')->name('admin.')->group(function () {
    // 儀表板
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // 校區管理
    Route::resource('campuses', CampusController::class);
    
    // 課程類型管理
    Route::resource('course-types', CourseTypeController::class);
    Route::post('course-types/{courseType}/duplicate', [CourseTypeController::class, 'duplicate'])->name('course-types.duplicate');
    Route::post('course-types/bulk-update', [CourseTypeController::class, 'bulkUpdate'])->name('course-types.bulk-update');
    
    // 課程管理
    Route::resource('courses', CourseController::class);
    Route::get('courses/get-campus-by-course-type', [CourseController::class, 'getCampusByCourseType'])->name('courses.get-campus-by-course-type');
    Route::get('courses/get-course-types-by-campus', [CourseController::class, 'getCourseTypesByCampus'])->name('courses.get-course-types-by-campus');
    
    // 校務事件管理
    Route::resource('school-events', SchoolEventController::class);
    Route::get('school-events/calendar', [SchoolEventController::class, 'calendar'])->name('school-events.calendar');
});
