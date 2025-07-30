@extends('layouts.admin')

@section('title', '課程管理')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>課程管理</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">首頁</a></li>
                        <li class="breadcrumb-item active">課程管理</li>
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
                            <h3 class="card-title">課程列表</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> 新增課程
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- 篩選表單 -->
                            <form method="GET" action="{{ route('admin.courses.index') }}" class="mb-3">
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
                                            <label for="course_type_id">課程類型</label>
                                            <select class="form-control" name="course_type_id" id="course_type_id">
                                                <option value="">全部課程類型</option>
                                                @foreach($courseTypes as $courseType)
                                                    <option value="{{ $courseType->id }}" {{ request('course_type_id') == $courseType->id ? 'selected' : '' }}>
                                                        {{ $courseType->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_from">開始日期</label>
                                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_to">結束日期</label>
                                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-info btn-sm">
                                                    <i class="fas fa-search"></i> 篩選
                                                </button>
                                                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary btn-sm">
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
                                            <th>課程類型</th>
                                            <th>上課校區</th>
                                            <th>主教老師</th>
                                            <th>助教老師</th>
                                            <th>上課日期</th>
                                            <th>上課時間</th>
                                            <th>容量/報名</th>
                                            <th>狀態</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($courses as $course)
                                        <tr>
                                            <td>{{ $course->id }}</td>
                                            <td>
                                                <strong>{{ $course->courseType->name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    @if($course->courseType->category === 'general')
                                                        <span class="badge badge-info">一般課程</span>
                                                    @else
                                                        <span class="badge badge-warning">比賽隊課程</span>
                                                    @endif
                                                    {{ $course->courseType->level ? ' - ' . $course->courseType->level : '' }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $course->campus->color }}; color: white;">
                                                    {{ $course->campus->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $course->mainTeacher->name ?? '未指派' }}</strong>
                                            </td>
                                            <td>
                                                @if($course->assistantTeachers->count() > 0)
                                                    @foreach($course->assistantTeachers as $assistant)
                                                        <span class="badge badge-secondary">{{ $assistant->name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">無</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $course->date->format('Y-m-d') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $course->date->format('l') }}</small>
                                            </td>
                                            <td>
                                                {{ $course->start_time->format('H:i') }} - {{ $course->end_time->format('H:i') }}
                                                <br>
                                                <small class="text-muted">{{ $course->courseType->duration }} 分鐘</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $course->has_available_seats ? 'success' : 'danger' }}">
                                                    {{ $course->current_enrollment }}/{{ $course->capacity }}
                                                </span>
                                                @if($course->has_available_seats)
                                                    <br>
                                                    <small class="text-success">剩餘 {{ $course->remaining_seats }} 名額</small>
                                                @else
                                                    <br>
                                                    <small class="text-danger">已額滿</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->date->isPast())
                                                    <span class="badge badge-secondary">已結束</span>
                                                @elseif($course->date->isToday())
                                                    <span class="badge badge-warning">今天</span>
                                                @else
                                                    <span class="badge badge-success">即將開始</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除這個課程嗎？')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">目前沒有課程資料</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- 分頁 -->
                            @if($courses->hasPages())
                                <div class="d-flex justify-content-center">
                                    {{ $courses->appends(request()->query())->links() }}
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

@push('scripts')
<script>
$(document).ready(function() {
    // 根據校區篩選課程類型
    $('#campus_id').change(function() {
        var campusId = $(this).val();
        var courseTypeSelect = $('#course_type_id');
        
        // 清空課程類型選項
        courseTypeSelect.html('<option value="">全部課程類型</option>');
        
        if (campusId) {
            // AJAX 獲取該校區的課程類型
            $.get('{{ route("admin.courses.get-course-types-by-campus") }}', {campus_id: campusId}, function(data) {
                data.forEach(function(courseType) {
                    courseTypeSelect.append('<option value="' + courseType.id + '">' + courseType.name + '</option>');
                });
            });
        }
    });
});
</script>
@endpush