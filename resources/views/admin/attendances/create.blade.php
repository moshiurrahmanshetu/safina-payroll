@extends('layouts.admin')
@section('title', 'Attendance Create')
@section('content')
<h3 class="page-header">Attendance Create {{link_to_route('attendances.index','Attendance List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model(Request::old(),array('route' => array('attendances.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}

    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Attendance Details</h4>
      </div>
   
      <div class="panel-body">
         <div class="row">
    <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Employee *</label>
          <select name="user_id" class="form-control" required>
            <option value="">Select Employee</option>
            @if($users->count() > 0)
              @foreach($users as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            @else
              <option value="" disabled>No eligible employees found (users with salary_processing=1 and status=Active)</option>
            @endif
          </select>
          {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
        </div>
      </div>
    <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Attendance Date *</label>
          <input type="date" name="attendance_date" class="form-control" required>
          {!! $errors->first('attendance_date', '<p class="text-danger">:message</p>') !!}
        </div>
      </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Check In Time</label>
              <input type="time" name="check_in" class="form-control">
              {!! $errors->first('check_in', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Check Out Time</label>
              <input type="time" name="check_out" class="form-control">
              {!! $errors->first('check_out', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Status *</label>
              <select name="status" class="form-control" required>
                @foreach(config('myhelpers.attendance_status') as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </select>
              {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label">Remarks</label>
          {{Form::textarea('remarks',null, array('class' => 'form-control', 'rows'=>'3'))}}
          {!! $errors->first('remarks', '<p class="text-danger">:message</p>') !!}
        </div>
      </div>
    </div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <button type="submit" class="btn btn-primary">
        Save Attendance
      </button>
    </div>
  </div>
</div>
{{ Form::close() }}
</div>

@endsection
@section('script')

@endsection
