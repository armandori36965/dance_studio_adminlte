@extends('layouts.admin')

@section('title', '新增校區')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>新增校區</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">首頁</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.campuses.index') }}">校區管理</a></li>
                        <li class="breadcrumb-item active">新增校區</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">校區資訊</h3>
                        </div>
                        <form action="{{ route('admin.campuses.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">校區名稱 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" 
                                                   placeholder="請輸入校區名稱" required>
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">校區類型 <span class="text-danger">*</span></label>
                                            <select class="form-control @error('type') is-invalid @enderror" 
                                                    id="type" name="type" required>
                                                <option value="">請選擇校區類型</option>
                                                <option value="school" {{ old('type') === 'school' ? 'selected' : '' }}>學校</option>
                                                <option value="cram_school" {{ old('type') === 'cram_school' ? 'selected' : '' }}>補習班</option>
                                            </select>
                                            @error('type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="color">代表色 <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="color" class="form-control @error('color') is-invalid @enderror" 
                                                       id="color" name="color" value="{{ old('color', '#3788d8') }}" 
                                                       style="width: 60px;">
                                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                                       id="color_text" value="{{ old('color', '#3788d8') }}" 
                                                       placeholder="#RRGGBB" readonly>
                                            </div>
                                            @error('color')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">請選擇校區的代表色，用於視覺化顯示</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">儲存校區</button>
                                <a href="{{ route('admin.campuses.index') }}" class="btn btn-secondary">取消</a>
                            </div>
                        </form>
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
    // 顏色選擇器同步
    $('#color').on('input', function() {
        $('#color_text').val($(this).val());
    });
    
    $('#color_text').on('input', function() {
        let color = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(color)) {
            $('#color').val(color);
        }
    });
});
</script>
@endpush