@extends('layouts.admin')

@php
// // $k = \App\Models\Todo::find(10)->moashermkmfs;
// dd($k);

@endphp

@section('content')
<!-- Bootstrap Select CSS - matching the JS version 1.13.14 loaded in admin layout -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css" />
<!-- Note: jQuery, Bootstrap JS, and Bootstrap Select JS are already loaded in admin.blade.php -->
<style type="text/css">
    .dropdown-toggle{
        height: 40px;
        width: 400px !important;
    }
</style>
<div class="container">

    <x-page-heading :title="'إنشاء مؤشر كفاءة وفعالية'"  />

<form method="post" action="{{ route('moashermkmf.store') }}">
    @csrf
<div class="form-group">
<label for="name">الإسم:</label>
<input type="text" class="form-control" name="name"/>
</div>

<div class="form-group">
    <label for="name">المبادرات</label>
    <select  class="selectpicker "  data-live-search="true" name="mubadara" >
        @foreach ($mubadaras as $mubadara)
        <option value="{{ $mubadara->id }}" @if($mubadara->id == $_GET['mubadara']) selected @endif>{{ $mubadara->name }}</option>
        @endforeach
    </select>
    </div>


    <div class="form-group">
        <label for="name">النوع</label>
        <select  class="selectpicker"  data-live-search="true" name="type">
           <option value="mk">مؤشر كفاءة</option>
           <option value="mf">مؤشر فعالية</option>

        </select>
        </div>

    <div class="form-group">
        <label for="reached">المحقق:</label>
        <input type="number" step="0.01" class="form-control" name="reached" id="reached"/>
    </div>

    <div class="form-group">
        <label for="target">المستهدف:</label>
        <input type="number" step="0.01" class="form-control" name="target"/>
    </div>

    <div class="form-group">
        <label for="calculation_type">نوع الحساب:</label>
        <select class="form-control" name="calculation_type" id="calculation_type">
            <option value="">اختر نوع الحساب</option>
            <option value="automatic">آلي</option>
            <option value="manual">يدوي</option>
            <option value="tasks">مهام</option>
        </select>
    </div>

    <div class="form-group d-none">
        <label for="the_vari">المتغير:</label>
        <input type="text" class="form-control" name="the_vari"/>
    </div>

    <div class="form-group">
        <label for="weight">الوزن:</label>
        <input type="number" step="0.01" class="form-control" name="weight"/>
    </div>

    <div class="form-group" id="calculation_variable_group" style="display:none;">
        <label for="calculation_variable">متغير الحساب:</label>
        <input type="text" class="form-control" name="calculation_variable" id="calculation_variable"/>
    </div>

 
<div class="form-group">
    <button class="btn-primary">حفظ</button>
</form>
</div>

@push('scripts')
<script>
// Handle calculation_type change and initialize Bootstrap Select
$(document).ready(function() {
    // Initialize Bootstrap Select
    setTimeout(function() {
        if ($('.selectpicker').length > 0) {
            $('.selectpicker').selectpicker({
                liveSearch: true,
                style: 'btn-outline-secondary',
                size: 10
            });
        }
    }, 300);
    
    $('#calculation_type').on('change', function() {
        var calculationType = $(this).val();
        var reachedField = $('#reached');
        var calculationVariableGroup = $('#calculation_variable_group');
        
        if (calculationType === 'automatic') {
            // Show calculation_variable, disable reached
            calculationVariableGroup.show();
            reachedField.prop('disabled', true).val('');
        } else if (calculationType === 'manual') {
            // Hide calculation_variable, enable reached
            calculationVariableGroup.hide();
            $('#calculation_variable').val('');
            reachedField.prop('disabled', false);
        } else if (calculationType === 'tasks') {
            // Hide calculation_variable, disable reached
            calculationVariableGroup.hide();
            $('#calculation_variable').val('');
            reachedField.prop('disabled', true).val('');
        } else {
            // Default: hide calculation_variable, enable reached
            calculationVariableGroup.hide();
            reachedField.prop('disabled', false);
        }
    });
    
    // Trigger on page load to set initial state
    $('#calculation_type').trigger('change');
});
</script>
@endpush

@endsection