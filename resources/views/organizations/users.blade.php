@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة مستخدمي المنظمة: {{ $organization->name }}</h1>
        <a href="{{ route('organizations.show', $organization) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> رجوع
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

    <div class="row">
        <!-- Add User Form -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">إضافة مستخدم</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('organizations.users.add', $organization) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="user_id">اختر المستخدم</label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">-- اختر مستخدم --</option>
                                @foreach($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role">الدور</label>
                            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="member">عضو</option>
                                <option value="manager">مشرف</option>
                                <option value="admin">مدير</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> إضافة المستخدم
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">المستخدمين الحاليين ({{ $organization->users->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد</th>
                                    <th>الدور</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organization->users as $user)
                                    <tr>
                                        <td>
                                            {{ $user->name }}
                                            @if($user->pivot->is_default)
                                                <span class="badge badge-success">افتراضي</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <form action="{{ route('organizations.users.role', $organization) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <select name="role" class="form-control form-control-sm" onchange="this.form.submit()">
                                                    <option value="member" {{ $user->pivot->role === 'member' ? 'selected' : '' }}>عضو</option>
                                                    <option value="manager" {{ $user->pivot->role === 'manager' ? 'selected' : '' }}>مشرف</option>
                                                    <option value="admin" {{ $user->pivot->role === 'admin' ? 'selected' : '' }}>مدير</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('organizations.users.remove', $organization) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا المستخدم من المنظمة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <button type="submit" class="btn btn-danger btn-sm" title="إزالة">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </form>
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
