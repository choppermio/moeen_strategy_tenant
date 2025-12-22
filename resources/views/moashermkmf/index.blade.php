
@extends('layouts.admin')

@php
// // $k = \App\Models\Todo::find(10)->moashermkmfs;
// dd($k);

@endphp

@section('content')

<!-- bootstrap cdn-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<div class="container">
    <x-page-heading :title="'مؤشرات الكفاءة والفعالية'"  />

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="moashermkmfTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="automatic-tab" data-toggle="tab" href="#automatic" role="tab" aria-controls="automatic" aria-selected="true">آلي</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="manual-tab" data-toggle="tab" href="#manual" role="tab" aria-controls="manual" aria-selected="false">يدوي</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab" aria-controls="tasks" aria-selected="false">مهام</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content mt-3">
        <!-- Automatic Tab -->
        <div class="tab-pane fade show active" id="automatic" role="tabpanel" aria-labelledby="automatic-tab">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>الإسم</th>
                        <th>المستهدف</th>
                        <th>المحقق</th>
                        <th>متغير الحساب</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($moashermkmfs->where('calculation_type', 'automatic') as $moashermkmf)
                    <tr>
                        <td>{{ $moashermkmf->name }}</td>
                        <td>{{ $moashermkmf->target }}</td>
                        <td>{{ $moashermkmf->reached }}</td>
                        <td>{{ $moashermkmf->calculation_variable }}</td>
                        <td>
                            @if (in_array(current_user_position()->id, explode(',', env('STRATEGY_CONTROL_ID'))))
                                <a href="{{ route('moashermkmf.edit', $moashermkmf->id) }}" class="btn btn-primary">التعديل</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Manual Tab -->
        <div class="tab-pane fade" id="manual" role="tabpanel" aria-labelledby="manual-tab">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>الإسم</th>
                        <th>المستهدف</th>
                        <th>المحقق</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($moashermkmfs->where('calculation_type', 'manual') as $moashermkmf)
                    <tr>
                        <td>{{ $moashermkmf->name }}</td>
                        <td>{{ $moashermkmf->target }}</td>
                        <td>{{ $moashermkmf->reached }}</td>
                        <td>
                            @if (in_array(current_user_position()->id, explode(',', env('STRATEGY_CONTROL_ID'))))
                                <a href="{{ route('moashermkmf.edit', $moashermkmf->id) }}" class="btn btn-primary">التعديل</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tasks Tab -->
        <div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>الإسم</th>
                        <th>المستهدف</th>
                        <th>المحقق</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($moashermkmfs->where('calculation_type', 'tasks') as $moashermkmf)
                    <tr>
                        <td>{{ $moashermkmf->name }}</td>
                        <td>{{ $moashermkmf->target }}</td>
                        <td>{{ $moashermkmf->reached }}</td>
                        <td>
                            @if (in_array(current_user_position()->id, explode(',', env('STRATEGY_CONTROL_ID'))))
                                <a href="{{ route('moashermkmf.edit', $moashermkmf->id) }}" class="btn btn-primary">التعديل</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

  
@endsection