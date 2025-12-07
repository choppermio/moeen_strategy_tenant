@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل المنظمة: {{ $organization->name }}</h1>
        <div>
            <a href="{{ route('organizations.edit', $organization) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('organizations.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">معلومات المنظمة</h6>
                </div>
                <div class="card-body text-center">
                    @if($organization->logo)
                        <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="mb-3" style="max-height: 120px; max-width: 200px;">
                    @else
                        <i class="fas fa-building fa-5x text-primary mb-3"></i>
                    @endif
                    
                    <h4>{{ $organization->name }}</h4>
                    <p class="text-muted"><code>{{ $organization->slug }}</code></p>
                    
                    @if($organization->is_active)
                        <span class="badge badge-success badge-lg">نشطة</span>
                    @else
                        <span class="badge badge-danger badge-lg">غير نشطة</span>
                    @endif

                    @if($organization->description)
                        <hr>
                        <p class="text-muted">{{ $organization->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">المستخدمين ({{ $organization->users->count() }})</h6>
                    <a href="{{ route('organizations.users', $organization) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-users-cog"></i> إدارة المستخدمين
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد</th>
                                    <th>الدور</th>
                                    <th>افتراضي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organization->users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $user->pivot->role === 'admin' ? 'danger' : ($user->pivot->role === 'manager' ? 'warning' : 'info') }}">
                                                {{ $user->pivot->role === 'admin' ? 'مدير' : ($user->pivot->role === 'manager' ? 'مشرف' : 'عضو') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->pivot->is_default)
                                                <i class="fas fa-check text-success"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">لا يوجد مستخدمين في هذه المنظمة</td>
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
@endsection
