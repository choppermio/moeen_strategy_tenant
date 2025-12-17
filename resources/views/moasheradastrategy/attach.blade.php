@extends('layouts.admin')

@section('content')
<!-- Bootstrap Select CSS - matching the JS version 1.13.14 loaded in admin layout -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" />
<!-- Note: jQuery, Bootstrap JS, and Bootstrap Select JS are already loaded in admin.blade.php -->
<style type="text/css">
    .dropdown-toggle{
        height: 40px;
        width: 100% !important;
    }
</style>
<div class="container">

    <x-page-heading :title="'ربط المؤشر الاستراتيجي بمؤشرات الكفاءة والفعالية'"  />

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">المؤشر الاستراتيجي: {{ $moasheradastrategy->name }}</h5>
            
            <form method="post" action="{{ route('moasheradastrategy.storeAttach', $moasheradastrategy->id) }}">
                @csrf
                
                <div class="form-group">
                    <label for="moashermkmfs">اختر مؤشرات الكفاءة والفعالية:</label>
                    <select class="selectpicker" multiple data-live-search="true" name="moashermkmfs[]" id="moashermkmfs" required>
                        @foreach ($moashermkmfs as $moashermkmf)
                            <option value="{{ $moashermkmf->id }}" 
                                {{ in_array($moashermkmf->id, $attached_ids) ? 'selected' : '' }}>
                                {{ $moashermkmf->name }} ({{ $moashermkmf->type == 'mk' ? 'كفاءة' : 'فعالية' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">حفظ الربط</button>
                    <a href="{{ route('moasheradastrategy.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>

            @if($moasheradastrategy->moashermkmfs->count() > 0)
            <hr>
            <h6>المؤشرات المرتبطة حالياً:</h6>
            <ul class="list-group">
                @foreach($moasheradastrategy->moashermkmfs as $moashermkmf)
                    <li class="list-group-item">
                        {{ $moashermkmf->name }} 
                        <span class="badge badge-{{ $moashermkmf->type == 'mk' ? 'primary' : 'success' }}">
                            {{ $moashermkmf->type == 'mk' ? 'مؤشر كفاءة' : 'مؤشر فعالية' }}
                        </span>
                    </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>

<script>
// Wait for all libraries to load, then initialize Bootstrap Select
$(window).on('load', function() {
    setTimeout(function() {
        // Destroy any existing Bootstrap Select instances first
        $('.selectpicker').selectpicker('destroy');
        
        // Initialize Bootstrap Select fresh
        $('.selectpicker').selectpicker({
            liveSearch: true,
            style: 'btn-outline-secondary',
            size: 10,
            actionsBox: true,
            selectedTextFormat: 'count > 3'
        });
    }, 100);
});
</script>

@endsection
