@extends('layouts.admin')
@section('title', 'Time Slot Edit')
@section('content')
<h3 class="page-header">Time Slot Edit {{link_to_route('time-slots.index','Time Slot List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model($time_slot,array('route' => array('time-slots.update',$time_slot->id),'method'=>'PUT','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Service *</label>
        <select name="service_id" class="form-control" required>
          <option value="">Select Service</option>
          @foreach($services as $id => $name)
            <option value="{{ $id }}" {{ $time_slot->service_id == $id ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
        {!! $errors->first('service_id', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Slot Name *</label>
        {{Form::text('name',null, array('class' => 'form-control','required'=>'required','placeholder'=>'e.g. Morning, Evening'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Start Time *</label>
        {{Form::time('start_time',null, array('class' => 'form-control','required'=>'required'))}}
        {!! $errors->first('start_time', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">End Time *</label>
        {{Form::time('end_time',null, array('class' => 'form-control','required'=>'required'))}}
        <small class="text-muted">Overnight slots allowed (e.g., 20:00 to 08:00)</small>
        {!! $errors->first('end_time', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Price (৳) *</label>
        {{Form::number('price',null, array('class' => 'form-control','required'=>'required','step'=>'0.01','min'=>'0'))}}
        {!! $errors->first('price', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Status *</label>
        {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('status', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      {{Form::submit('Update',array('class'=>'btn btn-success'))}}
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
@section('script')

@endsection
