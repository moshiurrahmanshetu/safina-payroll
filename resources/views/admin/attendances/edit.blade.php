@extends('layouts.admin')
@section('title', 'Attendance Edit')
@section('content')
<h3 class="page-header">Attendance Edit {{link_to_route('attendances.index','Attendance List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model($attendance,array('route' => array('attendances.update', $attendance->id),'enctype'=>'multipart/form-data','method'=>'PUT','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Attendance Details</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Employee *</label>
          <input type="text" class="form-control" value="{{ $attendance->user ? $attendance->user->name : 'N/A' }}" readonly>
          {{ Form::hidden('user_id', $attendance->user_id) }}
        </div>
        <div class="form-group">
          <label class="control-label">Attendance Date *</label>
          <input type="date" name="attendance_date" class="form-control" value="{{ $attendance->attendance_date }}" required>
          {!! $errors->first('attendance_date', '<p class="text-danger">:message</p>') !!}
        </div>
        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Check In Time</label>
              <input type="time" name="check_in" class="form-control" value="{{ $attendance->check_in }}">
              {!! $errors->first('check_in', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Check Out Time</label>
              <input type="time" name="check_out" class="form-control" value="{{ $attendance->check_out }}">
              {!! $errors->first('check_out', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Status *</label>
              <select name="status" class="form-control" required>
                @foreach(config('myhelpers.attendance_status') as $key => $value)
                  <option value="{{ $key }}" {{ $attendance->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
              </select>
              {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label">Remarks</label>
          {{Form::textarea('remarks',$attendance->remarks, array('class' => 'form-control', 'rows'=>'3'))}}
          {!! $errors->first('remarks', '<p class="text-danger">:message</p>') !!}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <button type="submit" class="btn btn-primary">
        Update Attendance
      </button>
    </div>
  </div>
</div>
{{ Form::close() }}
</div>

@endsection
@section('script')

@endsection
