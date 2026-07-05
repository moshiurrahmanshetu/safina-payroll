@extends('layouts.admin')
@section('title', 'Ticket Create')
@section('content')
<h3 class="page-header">Ticket Create {{link_to_route('tickets.index','Ticket List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model(Request::old(),array('route' => array('tickets.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Ticket Name *</label>
        {{Form::text('name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Price *</label>
        {{Form::number('price',null, array('class' => 'form-control','required'=>'required','step'=>'0.01'))}}
        {!! $errors->first('price', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
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
          Create Ticket
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
