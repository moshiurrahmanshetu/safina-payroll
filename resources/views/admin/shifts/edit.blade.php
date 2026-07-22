@extends('layouts.admin')
@section('title', 'Shift Edit')
@section('content')
<h3 class="page-header">Shift Edit {{link_to_route('shifts.index','Shift List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model($shift,array('route' => array('shifts.update', $shift->id),'enctype'=>'multipart/form-data','class'=>'form-horizontal','method'=>'PUT')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Shift Details</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Shift Name *</label>
              {{Form::text('name',null, array('class' => 'form-control', 'required'=>'required'))}}
              {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Status *</label>
              <select name="status" class="form-control" required>
                <option value="Active" {{ $shift->status == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ $shift->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
              {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Time Settings</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Start Time *</label>
              {{Form::time('start_time',null, array('class' => 'form-control', 'required'=>'required'))}}
              {!! $errors->first('start_time', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">End Time *</label>
              {{Form::time('end_time',null, array('class' => 'form-control', 'required'=>'required'))}}
              {!! $errors->first('end_time', '<p class="text-danger">:message</p>') !!}
              <small class="text-muted">If end time is earlier than start time, this will be marked as a cross-day shift.</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Grace Periods</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Late Grace Minutes</label>
              {{Form::number('late_grace_minutes',$shift->late_grace_minutes, array('class' => 'form-control', 'min'=>'0'))}}
              {!! $errors->first('late_grace_minutes', '<p class="text-danger">:message</p>') !!}
              <small class="text-muted">Minutes allowed after start time before marking as late.</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Early Leave Grace Minutes</label>
              {{Form::number('early_leave_grace_minutes',$shift->early_leave_grace_minutes, array('class' => 'form-control', 'min'=>'0'))}}
              {!! $errors->first('early_leave_grace_minutes', '<p class="text-danger">:message</p>') !!}
              <small class="text-muted">Minutes allowed before end time for early leave.</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Auto Checkout</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Auto Checkout After Minutes</label>
              {{Form::number('auto_checkout_after_minutes',null, array('class' => 'form-control', 'min'=>'0'))}}
              {!! $errors->first('auto_checkout_after_minutes', '<p class="text-danger">:message</p>') !!}
              <small class="text-muted">Optional: Auto-checkout after this many minutes from start time. Leave blank to disable.</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Remarks</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Remarks</label>
          {{Form::textarea('remarks',null, array('class' => 'form-control', 'rows'=>3))}}
          {!! $errors->first('remarks', '<p class="text-danger">:message</p>') !!}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Update Shift</button>
      <a href="{{ route('shifts.index') }}" class="btn btn-danger">Cancel</a>
    </div>
  </div>
</div>
{{ Form::close() }}

@endsection
