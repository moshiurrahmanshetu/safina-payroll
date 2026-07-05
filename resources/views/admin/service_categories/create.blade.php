@extends('layouts.admin')
@section('title', 'Service Category Create')
@section('content')
<h3 class="page-header">Service Category Create {{link_to_route('service_categories.index','Service Category List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model(Request::old(),array('route' => array('service_categories.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Category Name *</label>
        {{Form::text('name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Status</label>
        {{Form::select('status',config('myhelpers.status'),1,array('class' => 'form-control'))}}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Create Service Category
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
