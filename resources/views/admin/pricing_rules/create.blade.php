@extends('layouts.admin')
@section('title', 'Pricing Rule Create')
@section('content')
<h3 class="page-header">Pricing Rule Create {{link_to_route('pricing_rules.index','Pricing Rule List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model(Request::old(),array('route' => array('pricing_rules.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Service *</label>
        <select name="service_id" class="form-control" required>
          <option value="">Select Service</option>
          @foreach($services as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
          @endforeach
        </select>
        {!! $errors->first('service_id', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Rule Type *</label>
        {{Form::select('rule_type',config('myhelpers.rule_type'),null,array('class' => 'form-control', 'required'=>'required','onchange'=>'toggleRuleTypeFields(this.value)'))}}
        {!! $errors->first('rule_type', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <!-- Seasonal/Holiday Date Fields -->
  <div id="date_fields" class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Start Date</label>
        {{Form::date('start_date',null, array('class' => 'form-control'))}}
        {!! $errors->first('start_date', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">End Date</label>
        {{Form::date('end_date',null, array('class' => 'form-control'))}}
        {!! $errors->first('end_date', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <!-- Weekend Days Selection -->
  <div id="weekend_fields" class="col-md-12 multi-column" style="display:none;">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Select Days</label>
        <div class="checkbox">
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'sat', false) }} Saturday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'sun', false) }} Sunday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'mon', false) }} Monday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'tue', false) }} Tuesday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'wed', false) }} Wednesday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'thu', false) }} Thursday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'fri', false) }} Friday</label>
        </div>
        {!! $errors->first('days', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Price Type *</label>
        {{Form::select('price_type',config('myhelpers.price_adjustment_type'),null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('price_type', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Amount *</label>
        {{Form::number('amount',null, array('class' => 'form-control','required'=>'required','step'=>'0.01'))}}
        {!! $errors->first('amount', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-4">
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
          Create Pricing Rule
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection

@section('script')
<script>
function toggleRuleTypeFields(rule_type){
  if(rule_type == '1'){ // Weekend
    document.getElementById('date_fields').style.display = 'none';
    document.getElementById('weekend_fields').style.display = 'block';
  }else{ // Seasonal or Holiday
    document.getElementById('date_fields').style.display = 'block';
    document.getElementById('weekend_fields').style.display = 'none';
  }
  
  // Holiday uses single date
  if(rule_type == '2'){
    // For holiday, end date is not needed (optional)
  }
}
</script>
@endsection
