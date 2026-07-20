@extends('layouts.admin')
@section('title', 'Assign Employee Shift')
@section('content')
<h3 class="page-header">Assign Employee Shift</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        {{ Form::open(array('route' => 'employee_shifts.store', 'method'=>'POST', 'class'=>'form-horizontal')) }}
        
        <div class="form-group">
          <label class="col-md-3 control-label">Employee</label>
          <div class="col-md-6">
            {{ Form::select('user_id', $employees, null, ['class' => 'form-control', 'required']) }}
            @if($errors->has('user_id'))
              <span class="text-danger">{{ $errors->first('user_id') }}</span>
            @endif
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-3 control-label">Shift</label>
          <div class="col-md-6">
            {{ Form::select('shift_id', $shifts, null, ['class' => 'form-control', 'required']) }}
            @if($errors->has('shift_id'))
              <span class="text-danger">{{ $errors->first('shift_id') }}</span>
            @endif
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-3 control-label">Effective From</label>
          <div class="col-md-6">
            {{ Form::date('effective_from', null, ['class' => 'form-control', 'required']) }}
            @if($errors->has('effective_from'))
              <span class="text-danger">{{ $errors->first('effective_from') }}</span>
            @endif
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-3 control-label">Effective To</label>
          <div class="col-md-6">
            {{ Form::date('effective_to', null, ['class' => 'form-control']) }}
            @if($errors->has('effective_to'))
              <span class="text-danger">{{ $errors->first('effective_to') }}</span>
            @endif
            <small class="text-muted">Leave empty for ongoing assignment</small>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-3 control-label">Is Default</label>
          <div class="col-md-6">
            {{ Form::checkbox('is_default', 1, false) }}
            <small class="text-muted">Mark as default shift for this employee</small>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-3 control-label">Remarks</label>
          <div class="col-md-6">
            {{ Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => 3]) }}
            @if($errors->has('remarks'))
              <span class="text-danger">{{ $errors->first('remarks') }}</span>
            @endif
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-6 col-md-offset-3">
            {{ Form::submit('Assign Shift', ['class' => 'btn btn-success']) }}
            <a href="{{ route('employee_shifts.index') }}" class="btn btn-danger">Cancel</a>
          </div>
        </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

@endsection
