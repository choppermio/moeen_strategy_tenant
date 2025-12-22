@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<div class="container">
    <x-page-heading :title="'إدارة أوزان المؤشرات الاستراتيجية'"  />

    @if ($errors->has('weight_error'))
        <div class="alert alert-danger">
            {{ $errors->first('weight_error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">الهدف الاستراتيجي: {{ $hadafstrategy->name }}</h5>
            
            <div class="alert alert-info">
                <strong>ملاحظة:</strong> يجب أن يكون مجموع الأوزان للمؤشرات الاستراتيجية مساوياً لـ 100%
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

            <form method="post" action="{{ route('hadafstrategy.updateWeights', $hadafstrategy->id) }}">
                @csrf
                @method('PUT')
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المؤشر الاستراتيجي</th>
                                <th style="width: 200px;">الوزن (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($moasheradastrategies as $moasheradastrategy)
                            <tr>
                                <td>{{ $moasheradastrategy->name }}</td>
                                <td>
                                    <input type="number" 
                                           step="0.01" 
                                           class="form-control weight-input" 
                                           name="weights[{{ $moasheradastrategy->id }}]" 
                                           value="{{ $moasheradastrategy->weight }}"
                                           data-id="{{ $moasheradastrategy->id }}"
                                           required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td><strong>المجموع</strong></td>
                                <td><strong id="total-weight">0%</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="submit-btn">حفظ الأوزان</button>
                    <a href="{{ route('hadafstrategies.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate initial weight sum
    calculateWeightSum();
    
    // Calculate weight sum whenever any weight input changes
    $('.weight-input').on('input', function() {
        calculateWeightSum();
    });
    
    // Prevent form submission if total is not 100
    $('form').on('submit', function(e) {
        var total = 0;
        $('.weight-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        
        if (total !== 100) {
            e.preventDefault();
            alert('مجموع الأوزان يجب أن يكون مساوياً لـ 100%. المجموع الحالي: ' + total + '%');
            return false;
        }
    });
});

function calculateWeightSum() {
    var totalWeight = 0;
    
    $('.weight-input').each(function() {
        totalWeight += parseFloat($(this).val()) || 0;
    });
    
    var remaining = 100 - totalWeight;
    
    // Update text display
    var displayElement = $('#weight-sum-display');
    displayElement.text('مجموع الأوزان: ' + totalWeight.toFixed(2) + ' من 100%');
    
    // Update total in table
    $('#total-weight').text(totalWeight.toFixed(2) + '%');
    
    // Update progress bar
    var progressBar = $('#weight-progress-bar');
    var progressText = $('#progress-text');
    progressBar.css('width', Math.min(totalWeight, 100) + '%');
    progressBar.attr('aria-valuenow', totalWeight);
    progressText.text(totalWeight.toFixed(2) + '%');
    
    // Update remaining display
    var remainingDisplay = $('#remaining-display');
    if (remaining > 0) {
        remainingDisplay.text('المتبقي: ' + remaining.toFixed(2) + '%');
    } else if (remaining < 0) {
        remainingDisplay.text('تجاوز: ' + Math.abs(remaining).toFixed(2) + '%');
    } else {
        remainingDisplay.text('تم الوصول إلى 100%');
    }
    
    // Color coding
    if (Math.abs(totalWeight - 100) < 0.01) { // Allow small floating point differences
        displayElement.removeClass('text-danger text-warning').addClass('text-success');
        progressBar.removeClass('bg-danger bg-warning').addClass('bg-success');
        remainingDisplay.removeClass('text-danger').addClass('text-success');
        $('#submit-btn').prop('disabled', false);
    } else if (totalWeight > 100) {
        displayElement.removeClass('text-success text-warning').addClass('text-danger');
        progressBar.removeClass('bg-success bg-warning').addClass('bg-danger');
        remainingDisplay.removeClass('text-success').addClass('text-danger');
        $('#submit-btn').prop('disabled', true);
    } else {
        displayElement.removeClass('text-danger text-success').addClass('text-warning');
        progressBar.removeClass('bg-danger bg-success').addClass('bg-warning');
        remainingDisplay.removeClass('text-danger text-success').addClass('text-warning');
        $('#submit-btn').prop('disabled', true);
    }
}
</script>
@endpush

@endsection
