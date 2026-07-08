@extends('layouts.admin')
@section('content') 
<h1 class="page-header">User Create {{link_to_route('users.index','User lists',null,array('class'=>'btn btn-success pull-right'))}}</h1>
<div class="row">
{{ Form::model(Request::old(),array('route' => array('users.store'),'enctype'=>'multipart/form-data','method' => 'PUT','class'=>'form-horizontal')) }}
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label for="name" class="control-label">Name <sup>*</sup></label>
        {{Form::text('name',null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="designation_id" class="control-label">Designation <sup>*</sup></label>
        {{Form::select('designation_id',$designations,null,array('class' => 'form-control', 'required'=>'required'))}}
        {{ $errors->first('designation_id', '<p class="text-danger">:message</p>' ) }}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Directorate <sup>*</sup></label>
        {{Form::select('department_id',$departments,null,array('class' => 'form-control', 'required'=>'required'))}}
        {{ $errors->first('department_id', '<p class="text-danger">:message</p>' ) }}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="role_id" class="control-label">Role <sup>*</sup></label>
        {{Form::select('role_id',$roles,null,array('class' => 'form-control', 'required'=>'required'))}} 
        {{ $errors->first('role_id', '<p class="text-danger">:message</p>' ) }}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Supervisor</label>
        {{Form::select('supervisor_id',array('0'=>'No Supervisor')+$supervisors,null,array('class' => 'form-control'))}}
        {{ $errors->first('supervisor_id', '<p class="text-danger">:message</p>' ) }}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="email" class="control-label">E-Mail Address <sup>*</sup></label>
        {{Form::email('email',null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('email', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label for="password" class="control-label">Password <sup>*</sup></label>
        {{Form::password('password',array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('password', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="password_confirmation" class="control-label">Confirm Password <sup>*</sup></label>
        {{Form::password('password_confirmation',array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('password_confirmation', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Mobile # <sup>*</sup></label>
        {{Form::text('mobile_no',null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('mobile_no', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label for="status" class="control-label">Status</label>
        {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label for="salary_processing" class="control-label">Salary Processing</label>
        {{Form::select('salary_processing',config('myhelpers.salary_processing'),0,array('class' => 'form-control'))}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Address</label>
        {{Form::text('address',null,array('class' => 'form-control'))}}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="col-md-7 form-group">
        <label class="control-label">Photo <sub>Max 1 MB size, .jpeg | .png | .jpg | .gif | .svg image format allow</sub></label>
        {{Form::file('photo',array('class' => 'form-control', 'onChange'=>'readURL(this)'))}}
        {!! $errors->first('photo', '<p class="text-danger">:message</p>' ) !!}
      </div>
      <div class="col-md-5 preview-div">
      </div>
    </div>
    <div class="col-md-6">
      <div class="col-md-7 form-group">
        <label class="control-label">Signature <sub>Max 200 KB size, .jpeg | .png | .jpg | .gif | .svg image format allow</sub></label>
        {{Form::file('signature',array('class' => 'form-control', 'onChange'=>'readURL(this)'))}}
        {!! $errors->first('signature', '<p class="text-danger">:message</p>' ) !!}
      </div>
      <div class="col-md-5 preview-div">
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group"><br>
        <button type="submit" class="btn btn-primary">
          Register
        </button>
      </div>
    </div>
  </div>
  {{ Form::close() }}
</div>
@endsection
