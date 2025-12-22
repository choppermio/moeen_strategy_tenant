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

    @if ($errors->has('weight_error'))
        <div class="alert alert-danger">
            {{ $errors->first('weight_error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">الموشر الاستراتيجي: {{ $moasheradastrategy->name }}</h5>
            
            <div class="alert alert-info">
                <strong>ملاحظة:</strong> يجب أن يكون مجموع الأوزان للموشرات المختارة مساوياً لـ 100%
                <br>
                <div class="mt-2">
                    <span id="weight-sum-display" class="font-weight-bold" style="font-size: 1.1em;"></span>
                    <div class="progress mt-2" style="height: 25px;">
                        <div id="weight-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="progress-text"></span>
                        </div>
                    </div>
                    <small id="remaining-display" class="text-muted"></small>
                </div>
            </div>
            
            <form method="post" action="{{ route('moasheradastrategy.storeAttach', $moasheradastrategy->id) }}">
                @csrf
                
                <div class="form-group">
                    <label for="moashermkmfs">اختر مؤشرات الكفاءة والفعالية:</label>
                    <select class="selectpicker" multiple data-live-search="true" name="moashermkmfs[]" id="moashermkmfs" required>
                        @foreach ($moashermkmfs as $moashermkmf)
                            <option value="{{ $moashermkmf->id }}" 
                                {{ in_array($moashermkmf->id, $attached_ids) ? 'selected' : '' }}>
                                {{ $moashermkmf->name }} ({{ $moashermkmf->type == 'mk' ? 'كفاءة' : 'فعالية' }}) ({{$moashermkmf->weight}})
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
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>النوع</th>
                        <th>الوزن</th>
                        <th>النسبة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($moasheradastrategy->moashermkmfs as $moashermkmf)
                    <tr>
                        <td>{{ $moashermkmf->name }}</td>
                        <td>
                            <span class="badge badge-{{ $moashermkmf->type == 'mk' ? 'primary' : 'success' }}">
                                {{ $moashermkmf->type == 'mk' ? 'مؤشر كفاءة' : 'مؤشر فعالية' }}
                            </span>
                        </td>
                        <td>{{ $moashermkmf->weight }}%</td>
                        <td>{{ $moashermkmf->percentage }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Wait for all libraries to load, then initialize Bootstrap Select
$(document).ready(function() {
    setTimeout(function() {
        // Initialize Bootstrap Select
        if ($('.selectpicker').length > 0) {
            $('.selectpicker').selectpicker({
                liveSearch: true,
                style: 'btn-outline-secondary',
                size: 10,
                actionsBox: true,
                selectedTextFormat: 'count > 3'
            });
        }
        
        // Calculate initial weight sum
        calculateWeightSum();
    }, 300);
    
    // Calculate weight sum whenever selection changes
    $('#moashermkmfs').on('changed.bs.select', function() {
        calculateWeightSum();
    });
});

function calculateWeightSum() {
    var weights = @json($moashermkmfs->pluck('weight', 'id'));
    var selectedIds = $('#moashermkmfs').val() || [];
    var totalWeight = 0;
    
    selectedIds.forEach(function(id) {
        totalWeight += parseFloat(weights[id] || 0);
    });
    
    var remaining = 100 - totalWeight;
    
    // Update text display
    var displayElement = $('#weight-sum-display');
    displayElement.text('مجموع الأوزان: ' + totalWeight + ' من 100%');
    
    // Update progress bar
    var progressBar = $('#weight-progress-bar');
    var progressText = $('#progress-text');
    progressBar.css('width', totalWeight + '%');
    progressBar.attr('aria-valuenow', totalWeight);
    progressText.text(totalWeight + '%');
    
    // Update remaining display
    var remainingDisplay = $('#remaining-display');
    if (remaining > 0) {
        remainingDisplay.text('المتبقي: ' + remaining + '%');
    } else if (remaining < 0) {
        remainingDisplay.text('تجاوز: ' + Math.abs(remaining) + '%');
    } else {
        remainingDisplay.text('تم الوصول إلى 100%');
    }
    
    // Color coding
    if (totalWeight === 100) {
        displayElement.removeClass('text-danger text-warning').addClass('text-success');
        progressBar.removeClass('bg-danger bg-warning').addClass('bg-success');
        remainingDisplay.removeClass('text-danger').addClass('text-success');
    } else if (totalWeight > 100) {
        displayElement.removeClass('text-success text-warning').addClass('text-danger');
        progressBar.removeClass('bg-success bg-warning').addClass('bg-danger');
        remainingDisplay.removeClass('text-success').addClass('text-danger');
    } else {
        displayElement.removeClass('text-danger text-success').addClass('text-warning');
        progressBar.removeClass('bg-danger bg-success').addClass('bg-warning');
        remainingDisplay.removeClass('text-danger text-success').addClass('text-warning');
    }
}
</script>
@endpush

@endsection
