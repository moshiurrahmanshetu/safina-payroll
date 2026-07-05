@extends('layouts.admin')
@section('title', 'Parking Ticket Create')
@section('content')
<h3 class="page-header">Parking Ticket Create - <span class="text-success">({{$parkingCounterName}})</span> {{link_to_route('parking_tickets.index','Parking Ticket List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="alert alert-info">
  <i class="fa fa-info-circle"></i> After creating the ticket, a QR code will be generated. Scan the QR code to check-in when vehicle arrives and check-out when vehicle leaves.
</div>

{{ Form::model(Request::old(),array('route' => array('parking_tickets.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Vehicle Type *</label>
        {{Form::select('vehicle_id', $vehicles, null, array('class' => 'form-control', 'required'=>'required', 'id' => 'vehicle_id'))}}
        {!! $errors->first('vehicle_id', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Vehicle Number *</label>
        {{Form::text('vehicle_number',null, array('class' => 'form-control', 'required'=>'required', 'placeholder' => 'e.g. DH-123456'))}}
        {!! $errors->first('vehicle_number', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Driver Name *</label>
        {{Form::text('driver_name',null, array('class' => 'form-control', 'required'=>'required', 'placeholder' => 'Enter Driver Name'))}}
        {!! $errors->first('driver_name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Driver Phone *</label>
        {{Form::text('driver_phone',null, array('class' => 'form-control', 'required'=>'required', 'placeholder' => 'Enter Driver Phone'))}}
        {!! $errors->first('driver_phone', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Slot Price (Tk) *</label>
        {{Form::number('base_price',null, array('class' => 'form-control', 'required'=>'required', 'step'=>'0.01', 'min'=>'0', 'placeholder' => 'e.g. 100.00', 'id' => 'base_price', 'readonly' => 'readonly'))}}
        <small class="text-muted">Auto-filled based on vehicle type. One slot = 08:00 to 18:00 (10 hours). Additional slots charged for overstays.</small>
        {!! $errors->first('base_price', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg">
          <i class="fa fa-plus"></i> Create Parking Ticket
        </button>
        <a href="{{ route('parking_tickets.index') }}" class="btn btn-danger btn-lg">Cancel</a>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection

@section('script')
<script>
// Vehicle rates data from server
var vehicleRates = {};

$(document).ready(function() {
  // Fetch vehicle rates via AJAX
  $.ajax({
    url: '{{ route("vehicles.rates") }}',
    method: 'GET',
    success: function(data) {
      vehicleRates = data;
      // Set initial price if vehicle selected
      var selectedVehicle = $('#vehicle_id').val();
      if (selectedVehicle && vehicleRates[selectedVehicle]) {
        $('#base_price').val(vehicleRates[selectedVehicle]);
      }
    }
  });

  // On vehicle change, update base price
  $('#vehicle_id').on('change', function() {
    var vehicleId = $(this).val();
    if (vehicleId && vehicleRates[vehicleId]) {
      $('#base_price').val(vehicleRates[vehicleId]);
    } else {
      $('#base_price').val('');
    }
  });
});
</script>
@endsection
