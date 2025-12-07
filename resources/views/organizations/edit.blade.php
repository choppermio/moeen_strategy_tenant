@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل منظمة: {{ $organization->name }}</h1>
        <a href="{{ route('organizations.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">بيانات المنظمة</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('organizations.update', $organization) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">اسم المنظمة <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $organization->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="slug" class="col-sm-2 col-form-label">المعرف <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $organization->slug) }}" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label">الوصف</label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $organization->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="logo" class="col-sm-2 col-form-label">الشعار</label>
                    <div class="col-sm-10">
                        @if($organization->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $organization->logo) }}" alt="الشعار الحالي" style="max-height: 60px;">
                            </div>
                        @endif
                        <input type="file" class="form-control-file @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                        <small class="form-text text-muted">اختر صورة جديدة لتغيير الشعار الحالي</small>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ $organization->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                منظمة نشطة
                            </label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>
                        <a href="{{ route('organizations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
