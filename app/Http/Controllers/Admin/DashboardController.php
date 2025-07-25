<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     // 將以下整個方法複製貼上
    public function index()
    {
        return view('admin.dashboard');
    }
}
