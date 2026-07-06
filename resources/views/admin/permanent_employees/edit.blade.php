@extends('layouts.admin')
@section('title', 'Permanent Employee Edit')
@section('content')
<h3 class="page-header">Permanent Employee Edit {{link_to_route('permanent_employees.index','Permanent Employee List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model($permanent_employee,array('route' => array('permanent_employees.update', $permanent_employee->id),'method'=>'PUT','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Employee Information</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Employee ID</label>
          <p class="form-control-static"><strong>{{$permanent_employee->employee_id}}</strong></p>
        </div>

        @if($permanent_employee->user_id)
        <div class="form-group">
          <label class="control-label">Linked User</label>
          <p class="form-control-static">{{$permanent_employee->user ? $permanent_employee->user->name : 'N/A'}}</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Current Photo</label>
        @if($permanent_employee->photo)
          <img src="{{ asset($permanent_employee->photo) }}" alt="Current Photo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
        @else
          <span class="text-muted">No Photo</span>
        @endif
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Change Photo</label>
        {{Form::file('photo', array('class' => 'form-control'))}}
        {!! $errors->first('photo', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Full Name *</label>
        {{Form::text('full_name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('full_name', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Father's Name *</label>
        {{Form::text('father_name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('father_name', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Mother's Name *</label>
        {{Form::text('mother_name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('mother_name', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Gender *</label>
        {{Form::select('gender', ['Male'=>'Male', 'Female'=>'Female', 'Other'=>'Other'], null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('gender', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Date of Birth</label>
        {{Form::date('date_of_birth',null, array('class' => 'form-control datetimepicker1'))}}
        {!! $errors->first('date_of_birth', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">NID</label>
        {{Form::text('nid',null, array('class' => 'form-control'))}}
        {!! $errors->first('nid', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Mobile *</label>
        {{Form::text('mobile',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('mobile', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Email</label>
        {{Form::email('email',null, array('class' => 'form-control'))}}
        {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Emergency Contact</label>
        {{Form::text('emergency_contact',null, array('class' => 'form-control'))}}
        {!! $errors->first('emergency_contact', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Present Address</label>
        {{Form::textarea('present_address',null, array('class' => 'form-control', 'rows'=>'3'))}}
        {!! $errors->first('present_address', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Permanent Address</label>
        {{Form::textarea('permanent_address',null, array('class' => 'form-control', 'rows'=>'3'))}}
        {!! $errors->first('permanent_address', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Blood Group</label>
        {{Form::select('blood_group', [''=>'Select', 'A+'=>'A+', 'A-'=>'A-', 'B+'=>'B+', 'B-'=>'B-', 'AB+'=>'AB+', 'AB-'=>'AB-', 'O+'=>'O+', 'O-'=>'O-'], null, array('class' => 'form-control'))}}
        {!! $errors->first('blood_group', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Joining Date *</label>
        {{Form::date('joining_date',null, array('class' => 'form-control datetimepicker1', 'required'=>'required'))}}
        {!! $errors->first('joining_date', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Employment Status *</label>
        {{Form::select('employment_status',config('myhelpers.status'),null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('employment_status', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Department *</label>
        {{Form::select('department_id', [''=>'Select Department'] + $departments, null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('department_id', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Designation *</label>
        {{Form::select('designation_id', [''=>'Select Designation'] + $designations, null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('designation_id', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Notes</label>
        {{Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>'3'))}}
        {!! $errors->first('notes', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Update Permanent Employee
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection

@section('script')

@endsection
