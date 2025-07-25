<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController; // 加在檔案最上方

Route::get('/', function () {
    return view('welcome');
});
// 將下面這行加到檔案中
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
