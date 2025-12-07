@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">اختيار المنظمة</div>

                <div class="card-body">
                    <h5 class="mb-4">الرجاء اختيار المنظمة التي تريد العمل عليها:</h5>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        @foreach($organizations as $organization)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 organization-card" style="cursor: pointer;" onclick="selectOrganization({{ $organization->id }})">
                                    <div class="card-body text-center">
                                        @if($organization->logo)
                                            <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="mb-3" style="max-height: 80px; max-width: 150px;">
                                        @else
                                            <div class="mb-3">
                                                <i class="fas fa-building fa-4x text-primary"></i>
                                            </div>
                                        @endif
                                        <h5 class="card-title">{{ $organization->name }}</h5>
                                        @if($organization->description)
                                            <p class="card-text text-muted small">{{ Str::limit($organization->description, 100) }}</p>
                                        @endif
                                        <span class="badge badge-{{ $organization->pivot->role === 'admin' ? 'danger' : ($organization->pivot->role === 'manager' ? 'warning' : 'info') }}">
                                            {{ $organization->pivot->role === 'admin' ? 'مدير' : ($organization->pivot->role === 'manager' ? 'مشرف' : 'عضو') }}
                                        </span>
                                        @if($organization->pivot->is_default)
                                            <span class="badge badge-success">افتراضي</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="switch-form" action="{{ route('organization.switch') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="organization_id" id="organization_id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .organization-card:hover {
        border-color: #2797b6;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>

<script>
    function selectOrganization(orgId) {
        document.getElementById('organization_id').value = orgId;
        document.getElementById('switch-form').submit();
    }
</script>
@endsection
