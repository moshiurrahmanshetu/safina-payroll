@extends('layouts.admin')
@section('content') 
<h1 class="page-header">User Update {{link_to_route('users.index','User lists',null,array('class'=>'btn btn-success pull-right'))}}</h1>
<div class="row">
  {{ Form::model($user,array('route' => array('user.update', $user->id), 'enctype'=>'multipart/form-data', 'method' => 'PUT', 'class'=>'form-horizontal')) }}
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
        {{Form::select('salary_processing',config('myhelpers.salary_processing'),null,array('class' => 'form-control'))}}
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
        <label for="role_id" class="control-label">Photo <sub>Max 1 MB size, .jpeg | .png | .jpg | .gif | .svg image format allow</sub></label>
        {{Form::file('photo',array('class' => 'form-control', 'onChange'=>'readURL(this)'))}}  
        {!! $errors->first('photo', '<p class="text-danger">:message</p>' ) !!} 
        {{ Form::hidden('old_image',$user->photo) }} 
      </div>
      <div class="col-md-5 preview-div">
        {{ HTML::image('storage/app/admin/users/'.$user->photo, null, array('width'=>'70', 'class'=>'img-responsive')) }}
      </div>
    </div>

    <div class="col-md-6">
      <div class="col-md-7 form-group">
        <label for="role_id" class="control-label">Signature <sub>Max 200 KB size, .jpeg | .png | .jpg | .gif | .svg image format allow</sub></label>
        {{Form::file('signature',array('class' => 'form-control', 'onChange'=>'readURL(this)'))}}  
        {!! $errors->first('signature', '<p class="text-danger">:message</p>' ) !!} 
        {{ Form::hidden('old_signature',$user->signature) }} 
      </div>
      <div class="col-md-5 preview-div">
        {{ HTML::image('storage/app/admin/users/'.$user->signature, null, array('width'=>'70', 'class'=>'img-responsive')) }}
      </div>
    </div>
    
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group"><br>
        <button type="submit" class="btn btn-primary">
          Update
        </button>
      </div>
    </div>
  </div>
  {{ Form::close() }}
</div>

@if(checkMenuActive(['RegisterController@changeAllUserPassword'],$menu_list))
<br><br>
<hr><br>
<div class="row" style="border:2px solid #ccc; padding: 15px;">
  <h1 class="page-header">Change Password</h1>
  {{ Form::model(Request::old(),array('route' => array('users.changeAllUserPassword'), 'method' => 'PUT','class'=>'form-horizontal')) }}
  <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
    <label for="password" class="control-label col-sm-2">Password</label>
    <div class="col-md-6">
      <input id="password" type="password" class="form-control" name="password" required>
      <input type="hidden" name="user_id" value="{{$user->id}}" required>
      @if ($errors->has('password'))
      <span class="help-block">
        <strong>{{ $errors->first('password') }}</strong>
      </span>
      @endif
    </div>
  </div>
  <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
    <label for="password-confirm" class="control-label col-sm-2">Confirm Password</label>
    <div class="col-md-6">
      <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
      @if ($errors->has('password_confirmation'))
      <span class="help-block">
        <strong>{{ $errors->first('password_confirmation') }}</strong>
      </span>
      @endif
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-6 col-md-offset-2">
      <button type="submit" class="btn btn-danger">
        Change Password
      </button> &nbsp; <sub>If you change password then this user need to login by new password</sub>
    </div>
  </div>
  {{ Form::close() }}
</div>
@endif
@endsection