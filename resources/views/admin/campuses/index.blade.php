@extends('layouts.admin')

@section('title', '校區管理')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>校區管理</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">首頁</a></li>
                        <li class="breadcrumb-item active">校區管理</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">校區列表</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.campuses.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> 新增校區
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>校區名稱</th>
                                        <th>類型</th>
                                        <th>代表色</th>
                                        <th>課程數量</th>
                                        <th>課程類型數量</th>
                                        <th>事件數量</th>
                                        <th>建立時間</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($campuses as $campus)
                                    <tr>
                                        <td>{{ $campus->id }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $campus->color }}; color: white;">
                                                {{ $campus->name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($campus->type === 'school')
                                                <span class="badge badge-info">學校</span>
                                            @else
                                                <span class="badge badge-warning">補習班</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="color-preview" style="width: 20px; height: 20px; background-color: {{ $campus->color }}; border-radius: 3px; margin-right: 8px;"></div>
                                                <span>{{ $campus->color }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $campus->courses_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{ $campus->course_types_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $campus->school_events_count }}</span>
                                        </td>
                                        <td>{{ $campus->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.campuses.show', $campus) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.campuses.edit', $campus) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.campuses.destroy', $campus) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除這個校區嗎？')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">目前沒有校區資料</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
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
</style>
@endpush