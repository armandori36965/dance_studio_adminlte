@extends('layouts.admin')

@section('title', '校區詳情')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>校區詳情</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">首頁</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.campuses.index') }}">校區管理</a></li>
                        <li class="breadcrumb-item active">校區詳情</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">校區資訊</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.campuses.edit', $campus) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> 編輯
                                </a>
                                <a href="{{ route('admin.campuses.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> 返回列表
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 150px;">校區名稱：</th>
                                            <td>
                                                <span class="badge" style="background-color: {{ $campus->color }}; color: white; font-size: 14px;">
                                                    {{ $campus->name }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>校區類型：</th>
                                            <td>
                                                @if($campus->type === 'school')
                                                    <span class="badge badge-info">學校</span>
                                                @else
                                                    <span class="badge badge-warning">補習班</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>代表色：</th>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="color-preview" style="width: 30px; height: 30px; background-color: {{ $campus->color }}; border-radius: 5px; margin-right: 10px;"></div>
                                                    <span>{{ $campus->color }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>建立時間：</th>
                                            <td>{{ $campus->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>更新時間：</th>
                                            <td>{{ $campus->updated_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-chalkboard-teacher"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">課程數量</span>
                                            <span class="info-box-number">{{ $campus->courses->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-list"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">課程類型數量</span>
                                            <span class="info-box-number">{{ $campus->courseTypes->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">校務事件數量</span>
                                            <span class="info-box-number">{{ $campus->schoolEvents->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 課程類型列表 -->
                    @if($campus->courseTypes->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">相關課程類型</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>課程類型名稱</th>
                                        <th>分類</th>
                                        <th>程度等級</th>
                                        <th>時長</th>
                                        <th>價格</th>
                                        <th>狀態</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($campus->courseTypes as $courseType)
                                    <tr>
                                        <td>{{ $courseType->name }}</td>
                                        <td>
                                            @if($courseType->category === 'general')
                                                <span class="badge badge-info">一般課程</span>
                                            @else
                                                <span class="badge badge-warning">比賽隊課程</span>
                                            @endif
                                        </td>
                                        <td>{{ $courseType->level ?? '未設定' }}</td>
                                        <td>{{ $courseType->duration }} 分鐘</td>
                                        <td>${{ number_format($courseType->price, 0) }}</td>
                                        <td>
                                            @if($courseType->status === 'active')
                                                <span class="badge badge-success">啟用</span>
                                            @else
                                                <span class="badge badge-secondary">停用</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- 校務事件列表 -->
                    @if($campus->schoolEvents->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">相關校務事件</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>事件標題</th>
                                        <th>開始日期</th>
                                        <th>結束日期</th>
                                        <th>持續天數</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($campus->schoolEvents as $event)
                                    <tr>
                                        <td>{{ $event->title }}</td>
                                        <td>{{ $event->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $event->end_date->format('Y-m-d') }}</td>
                                        <td>{{ $event->duration_days }} 天</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .color-preview {
        border: 1px solid #ddd;
    }
    .info-box {
        margin-bottom: 15px;
    }
</style>
@endpush