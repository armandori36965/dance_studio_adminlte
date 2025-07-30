@extends('layouts.admin')

@section('title', '校務事件管理')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>校務事件管理</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">首頁</a></li>
                        <li class="breadcrumb-item active">校務事件管理</li>
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
                            <h3 class="card-title">校務事件列表</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.school-events.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> 新增校務事件
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- 篩選表單 -->
                            <form method="GET" action="{{ route('admin.school-events.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="campus_id">校區</label>
                                            <select class="form-control" name="campus_id" id="campus_id">
                                                <option value="">全部校區</option>
                                                @foreach($campuses as $campus)
                                                    <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                                        {{ $campus->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_from">開始日期</label>
                                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_to">結束日期</label>
                                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-info btn-sm">
                                                    <i class="fas fa-search"></i> 篩選
                                                </button>
                                                <a href="{{ route('admin.school-events.index') }}" class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-times"></i> 清除
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>事件標題</th>
                                            <th>發生校區</th>
                                            <th>開始日期</th>
                                            <th>結束日期</th>
                                            <th>持續天數</th>
                                            <th>狀態</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($schoolEvents as $event)
                                        <tr>
                                            <td>{{ $event->id }}</td>
                                            <td>
                                                <strong>{{ $event->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $event->full_title }}</small>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $event->campus->color }}; color: white;">
                                                    {{ $event->campus->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $event->start_date->format('Y-m-d') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $event->start_date->format('l') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $event->end_date->format('Y-m-d') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $event->end_date->format('l') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $event->duration_days }} 天</span>
                                                @if($event->is_single_day)
                                                    <br>
                                                    <small class="text-muted">單日事件</small>
                                                @else
                                                    <br>
                                                    <small class="text-muted">多日事件</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($event->end_date->isPast())
                                                    <span class="badge badge-secondary">已結束</span>
                                                @elseif($event->start_date->isPast() && $event->end_date->isFuture())
                                                    <span class="badge badge-warning">進行中</span>
                                                @else
                                                    <span class="badge badge-success">即將開始</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.school-events.show', $event) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.school-events.edit', $event) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.school-events.destroy', $event) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除這個校務事件嗎？')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">目前沒有校務事件資料</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- 分頁 -->
                            @if($schoolEvents->hasPages())
                                <div class="d-flex justify-content-center">
                                    {{ $schoolEvents->appends(request()->query())->links() }}
                                </div>
                            @endif
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
    .event-status {
        font-weight: bold;
    }
    .event-status.past {
        color: #6c757d;
    }
    .event-status.current {
        color: #ffc107;
    }
    .event-status.future {
        color: #28a745;
    }
</style>
@endpush