@extends('layouts.admin')
@section('title', 'Service Edit')
@section('content')
<h3 class="page-header">Service Edit {{link_to_route('services.index','Service List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model($services,array('route' => array('services.update',$services->id),'method'=>'PUT','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Service Category *</label>
        <select name="service_category_id" class="form-control" required>
          <option value="">Select Category</option>
          @foreach($service_categories as $id => $name)
            <option value="{{ $id }}" {{ $services->service_category_id == $id ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
        {!! $errors->first('service_category_id', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Service Name *</label>
        {{Form::text('name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Pricing Type *</label>
        {{Form::select('pricing_type',config('myhelpers.pricing_type'),null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('pricing_type', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Guest Capacity</label>
        {{Form::number('guest_capacity',null, array('class' => 'form-control', 'min' => '0'))}}
        <small class="text-muted">Maximum number of guests (optional)</small>
        {!! $errors->first('guest_capacity', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Service Details</label>
        {{Form::textarea('service_details',null, array('class' => 'form-control', 'rows' => '4'))}}
        <small class="text-muted">Detailed service description (optional)</small>
        {!! $errors->first('service_details', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
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
          Update Service
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
