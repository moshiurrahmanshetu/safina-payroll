@extends('layouts.admin')
@section('title', 'Pricing Rule Edit')
@section('content')
<h3 class="page-header">Pricing Rule Edit {{link_to_route('pricing_rules.index','Pricing Rule List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model($pricing_rule,array('route' => array('pricing_rules.update',$pricing_rule->id),'method'=>'PUT','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Service *</label>
        <select name="service_id" class="form-control" required>
          <option value="">Select Service</option>
          @foreach($services as $id => $name)
            <option value="{{ $id }}" {{ $pricing_rule->service_id == $id ? 'selected' : '' }}>{{ $name }}</option>
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
  <div id="date_fields" class="col-md-12 multi-column" style="{{$pricing_rule->rule_type == 1 ? 'display:none;' : 'display:block;'}}">
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
  <div id="weekend_fields" class="col-md-12 multi-column" style="{{$pricing_rule->rule_type == 1 ? 'display:block;' : 'display:none;'}}">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Select Days</label>
        <div class="checkbox">
          @php $selected_days = is_array($pricing_rule->days) ? $pricing_rule->days : json_decode($pricing_rule->days, true); @endphp
          @php $selected_days = is_array($selected_days) ? $selected_days : []; @endphp
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'sat', in_array('sat', $selected_days)) }} Saturday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'sun', in_array('sun', $selected_days)) }} Sunday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'mon', in_array('mon', $selected_days)) }} Monday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'tue', in_array('tue', $selected_days)) }} Tuesday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'wed', in_array('wed', $selected_days)) }} Wednesday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'thu', in_array('thu', $selected_days)) }} Thursday</label>
          <label style="margin-right: 15px;">{{ Form::checkbox('days[]', 'fri', in_array('fri', $selected_days)) }} Friday</label>
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
        {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Update Pricing Rule
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
}
</script>
@endsection
