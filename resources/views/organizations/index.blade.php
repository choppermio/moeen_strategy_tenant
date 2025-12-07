@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة المنظمات</h1>
        <a href="{{ route('organizations.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> إضافة منظمة جديدة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">قائمة المنظمات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الشعار</th>
                            <th>الاسم</th>
                            <th>المعرف</th>
                            <th>عدد المستخدمين</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizations as $organization)
                            <tr>
                                <td>{{ $organization->id }}</td>
                                <td>
                                    @if($organization->logo)
                                        <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" style="max-height: 40px; max-width: 80px;">
                                    @else
                                        <i class="fas fa-building text-muted"></i>
                                    @endif
                                </td>
                                <td>{{ $organization->name }}</td>
                                <td><code>{{ $organization->slug }}</code></td>
                                <td>{{ $organization->users_count }}</td>
                                <td>
                                    @if($organization->is_active)
                                        <span class="badge badge-success">نشطة</span>
                                    @else
                                        <span class="badge badge-danger">غير نشطة</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('organizations.show', $organization) }}" class="btn btn-info btn-sm" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('organizations.edit', $organization) }}" class="btn btn-warning btn-sm" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('organizations.users', $organization) }}" class="btn btn-primary btn-sm" title="المستخدمين">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <form action="{{ route('organizations.destroy', $organization) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المنظمة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد منظمات مسجلة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $organizations->links() }}
        </div>
    </div>
</div>
@endsection
