@extends('layouts.admin')
@section('title', 'Service Category Edit')
@section('content')
<h3 class="page-header">Service Category Edit {{link_to_route('service_categories.index','Service Category List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model($category,array('route' => array('service_categories.update',$category->id),'method'=>'PUT','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
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
        {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Update Service Category
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
