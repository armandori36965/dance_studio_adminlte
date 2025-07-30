@extends('layouts.admin')

@section('title', '課程類型管理')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>課程類型管理</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">首頁</a></li>
                        <li class="breadcrumb-item active">課程類型管理</li>
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
                            <h3 class="card-title">課程類型列表</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.course-types.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> 新增課程類型
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- 批量操作 -->
                            <form id="bulk-form" action="{{ route('admin.course-types.bulk-update') }}" method="POST" style="margin-bottom: 15px;">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" name="action" id="bulk-action">
                                            <option value="">選擇操作</option>
                                            <option value="activate">啟用選中項目</option>
                                            <option value="deactivate">停用選中項目</option>
                                            <option value="delete">刪除選中項目</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-warning" id="bulk-submit" disabled>
                                            <i class="fas fa-cogs"></i> 執行
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>ID</th>
                                            <th>課程類型名稱</th>
                                            <th>所屬校區</th>
                                            <th>分類</th>
                                            <th>程度等級</th>
                                            <th>時長</th>
                                            <th>價格</th>
                                            <th>最大學生數</th>
                                            <th>狀態</th>
                                            <th>課程數量</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($courseTypes as $courseType)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="course_type_ids[]" value="{{ $courseType->id }}" class="course-type-checkbox">
                                            </td>
                                            <td>{{ $courseType->id }}</td>
                                            <td>
                                                <strong>{{ $courseType->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $courseType->campus->color }}; color: white;">
                                                    {{ $courseType->campus->name }}
                                                </span>
                                            </td>
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
                                            <td>{{ $courseType->max_students }} 人</td>
                                            <td>
                                                @if($courseType->status === 'active')
                                                    <span class="badge badge-success">啟用</span>
                                                @else
                                                    <span class="badge badge-secondary">停用</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $courseType->courses_count }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.course-types.show', $courseType) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.course-types.edit', $courseType) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.course-types.duplicate', $courseType) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-secondary" title="複製">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.course-types.destroy', $courseType) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除這個課程類型嗎？')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="12" class="text-center">目前沒有課程類型資料</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 全選功能
    $('#select-all').change(function() {
        $('.course-type-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkSubmit();
    });

    // 個別選擇框變更
    $('.course-type-checkbox').change(function() {
        updateBulkSubmit();
        
        // 檢查是否全選
        var totalCheckboxes = $('.course-type-checkbox').length;
        var checkedCheckboxes = $('.course-type-checkbox:checked').length;
        
        if (checkedCheckboxes === totalCheckboxes) {
            $('#select-all').prop('checked', true);
        } else {
            $('#select-all').prop('checked', false);
        }
    });

    // 更新批量提交按鈕狀態
    function updateBulkSubmit() {
        var checkedCount = $('.course-type-checkbox:checked').length;
        var selectedAction = $('#bulk-action').val();
        
        if (checkedCount > 0 && selectedAction) {
            $('#bulk-submit').prop('disabled', false);
        } else {
            $('#bulk-submit').prop('disabled', true);
        }
    }

    // 批量操作選擇變更
    $('#bulk-action').change(function() {
        updateBulkSubmit();
    });

    // 批量操作確認
    $('#bulk-form').submit(function(e) {
        var action = $('#bulk-action').val();
        var checkedCount = $('.course-type-checkbox:checked').length;
        
        if (action === 'delete') {
            if (!confirm('確定要刪除選中的 ' + checkedCount + ' 個課程類型嗎？此操作無法復原！')) {
                e.preventDefault();
                return false;
            }
        } else {
            if (!confirm('確定要對選中的 ' + checkedCount + ' 個課程類型執行此操作嗎？')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endpush