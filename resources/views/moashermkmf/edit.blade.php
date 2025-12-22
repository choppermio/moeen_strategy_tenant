@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<style type="text/css">
    .dropdown-toggle{
        height: 40px;
        width: 400px !important;
    }
</style>
<div class="container">

    <x-page-heading :title="'تعديل مؤشر كفاءة وفعالية'"  />

    <!-- Change the action to point to the update route, assuming you have the indicator's ID -->
    <form method="post" action="{{ route('moashermkmf.update', $moashermkmf->id) }}">
        @csrf
        @method('PUT') <!-- Include this to simulate a PUT request -->

        <div class="form-group">
            <label for="name">الإسم:</label>
            <!-- Pre-fill the name -->
            <input type="text" class="form-control" name="name" value="{{ $moashermkmf->name }}"/>
        </div>
{{-- 
        <div class="form-group">
            <label for="name">المبادرات</label>
            <select class="selectpicker" data-live-search="true" name="mubadara">
                @foreach ($mubadaras as $mubadara)
                <option value="{{ $mubadara->id }}" {{ $moashermkmf->mubadara_id == $mubadara->id ? 'selected' : '' }}>{{ $mubadara->name }}</option>
                @endforeach
            </select>
        </div> --}}

        <div class="form-group">
            <label for="name">النوع</label>
            <select class="selectpicker" data-live-search="true" name="type">
               <option value="mk" {{ $moashermkmf->type == 'mk' ? 'selected' : '' }}>مؤشر كفاءة</option>
               <option value="mf" {{ $moashermkmf->type == 'mf' ? 'selected' : '' }}>مؤشر فعالية</option>
            </select>
        </div>

        <div class="form-group">
            <label for="reached">المحقق:</label>
            <input type="number" step="0.01" class="form-control" name="reached" id="reached" value="{{ $moashermkmf->reached }}"/>
        </div>

        <div class="form-group">
            <label for="target">المستهدف:</label>
            <input type="number" step="0.01" class="form-control" name="target" value="{{ $moashermkmf->target }}"/>
        </div>

        <div class="form-group">
            <label for="calculation_type">نوع الحساب:</label>
            <select class="form-control" name="calculation_type" id="calculation_type">
                <option value="">اختر نوع الحساب</option>
                <option value="automatic" {{ $moashermkmf->calculation_type == 'automatic' ? 'selected' : '' }}>آلي</option>
                <option value="manual" {{ $moashermkmf->calculation_type == 'manual' ? 'selected' : '' }}>يدوي</option>
                <option value="tasks" {{ $moashermkmf->calculation_type == 'tasks' ? 'selected' : '' }}>مهام</option>
            </select>
        </div>

        <div class="form-group d-none">
            <label for="the_vari">المتغير:</label>
            <input type="text" class="form-control" name="the_vari" value="{{ $moashermkmf->the_vari }}"/>
        </div>

        <div class="form-group">
            <label for="weight">الوزن:</label>
            <input type="number" step="0.01" class="form-control" name="weight" value="{{ $moashermkmf->weight }}"/>
        </div>

        <div class="form-group" id="calculation_variable_group">
            <label for="calculation_variable">متغير الحساب:</label>
            <input type="text" class="form-control" name="calculation_variable" id="calculation_variable" value="{{ $moashermkmf->calculation_variable }}"/>
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">تحديث</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Handle calculation_type change
$(document).ready(function() {
    $('#calculation_type').on('change', function() {
        var calculationType = $(this).val();
        var reachedField = $('#reached');
        var calculationVariableGroup = $('#calculation_variable_group');
        
        if (calculationType === 'automatic') {
            // Show calculation_variable, disable reached
            calculationVariableGroup.show();
            reachedField.prop('disabled', true);
        } else if (calculationType === 'manual') {
            // Hide calculation_variable, enable reached
            calculationVariableGroup.hide();
            $('#calculation_variable').val('');
            reachedField.prop('disabled', false);
        } else if (calculationType === 'tasks') {
            // Hide calculation_variable, disable reached
            calculationVariableGroup.hide();
            $('#calculation_variable').val('');
            reachedField.prop('disabled', true);
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
