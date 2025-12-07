@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">لا توجد منظمة</div>

                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-5x text-warning"></i>
                    </div>
                    
                    <h4 class="mb-3">لم يتم تعيينك لأي منظمة</h4>
                    
                    <p class="text-muted">
                        للوصول إلى النظام، يجب أن يتم تعيينك لمنظمة واحدة على الأقل.
                        <br>
                        يرجى التواصل مع مسؤول النظام لإضافتك إلى منظمة.
                    </p>

                    <hr>

                    <div class="mt-4">
                        <a href="{{ route('logout') }}" class="btn btn-secondary"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                        </a>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
