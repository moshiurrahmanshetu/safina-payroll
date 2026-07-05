@extends('layouts.admin')
@section('title', 'Package Sale Create')
@section('content')
<h3 class="page-header">Package Sale Create - <span class="text-success">({{$packageCounterName}})</span></h3>

<div class="row">
  <div class="col-md-10 col-md-offset-1">
    {{ Form::open(array('route' => 'package_bookings.store', 'id' => 'bookingForm')) }}

    <div class="row">
      <!-- Left Column - Booking Details -->
      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-heading">Package Sale Details</div>
          <div class="panel-body">

            <!-- Package Select -->
            <div class="form-group{{ $errors->has('package_id') ? ' has-error' : '' }}">
              {{ Form::label('package_id', 'Select Package *') }}
              {{ Form::select('package_id', ['' => '-- Select Package --'] + $packages, old('package_id'), ['class' => 'form-control', 'required' => 'required', 'id' => 'package_id']) }}
              @if ($errors->has('package_id'))
                <span class="help-block">{{ $errors->first('package_id') }}</span>
              @endif
            </div>

            <!-- Date -->
            <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
              {{ Form::label('date', 'Date *') }}
              {{ Form::text('date', old('date', date('d-m-Y')), ['class' => 'form-control datepicker', 'required' => 'required', 'placeholder' => 'DD-MM-YYYY']) }}
              @if ($errors->has('date'))
                <span class="help-block">{{ $errors->first('date') }}</span>
              @endif
            </div>

            <!-- Quantity -->
            <div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
              {{ Form::label('quantity', 'Quantity *') }}
              {{ Form::number('quantity', old('quantity', 1), ['class' => 'form-control', 'required' => 'required', 'min' => '1', 'id' => 'quantity']) }}
            </div>

            <!-- Extra Person -->
            <div class="form-group{{ $errors->has('extra_person') ? ' has-error' : '' }}">
              {{ Form::label('extra_person', 'Extra Person') }}
              {{ Form::number('extra_person', old('extra_person', 0), ['class' => 'form-control', 'min' => '0', 'id' => 'extra_person']) }}
              <small class="text-muted">Additional persons beyond package default</small>
            </div>

          </div>
        </div>

        <!-- Amount Summary -->
        <div class="panel panel-success">
          <div class="panel-heading">Amount Summary</div>
          <div class="panel-body">
            <table class="table table-condensed">
              <tr>
                <td>Base Amount:</td>
                <td class="text-right"><strong>৳<span id="base_amount">0.00</span></strong></td>
              </tr>
              <tr>
                <td>Extra Person Amount:</td>
                <td class="text-right"><strong>৳<span id="extra_person_amount">0.00</span></strong></td>
              </tr>
              <tr class="success">
                <td><strong>Final Amount:</strong></td>
                <td class="text-right"><strong>৳<span id="final_amount">0.00</span></strong></td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <!-- Right Column - Tickets -->
      <div class="col-md-6">
        <!-- Included Tickets (Readonly) -->
        <div class="panel panel-info">
          <div class="panel-heading">Included Tickets (Package)</div>
          <div class="panel-body" id="included_tickets_container">
            <p class="text-muted">Select a package to see included tickets</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 text-center mb-4 mt-4">
        {{ Form::submit('Create Package Sale', array('class' => 'btn btn-primary btn-lg')) }}
        {{ link_to_route('package_bookings.index', 'Cancel', [], array('class' => 'btn btn-danger btn-lg')) }}
      </div>
    </div>

    {{ Form::close() }}
  </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function(){
  // Initialize datepicker
  $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    todayHighlight: true
  });

  // Package change handler
  $('#package_id').change(function(){
    var packageId = $(this).val();
    if(packageId){
      loadPackageDetails(packageId);
    } else {
      $('#included_tickets_container').html('<p class="text-muted">Select a package to see included tickets</p>');
      resetCalculations();
    }
  });

  // Quantity/Extra person change handlers
  $('#quantity, #extra_person').on('input', function(){
    calculateTotals();
  });
});

var packageData = null;

function loadPackageDetails(packageId){
  $.ajax({
    url: '{{ url("admin/package_bookings/get-package") }}/' + packageId,
    type: 'GET',
    dataType: 'json',
    success: function(response){
      if(response.success){
        packageData = response.package;
        displayIncludedTickets(response.items);
        calculateTotals();
      }
    },
    error: function(){
      alert('Error loading package details');
    }
  });
}

function displayIncludedTickets(items){
  var html = '<ul class="list-group">';
  if(items.length > 0){
    $.each(items, function(index, item){
      html += '<li class="list-group-item">' +
                '<span class="badge">Included</span>' +
                item.ticket_name +
              '</li>';
    });
  } else {
    html += '<li class="list-group-item text-muted">No tickets included</li>';
  }
  html += '</ul>';
  $('#included_tickets_container').html(html);
}

function calculateTotals(){
  if(!packageData){
    resetCalculations();
    return;
  }

  var quantity = parseInt($('#quantity').val()) || 1;
  var extraPerson = parseInt($('#extra_person').val()) || 0;

  // Base amount = base_price × quantity
  var baseAmount = packageData.base_price * quantity;

  // Extra amount = extra_person × extra_person_price
  var extraAmount = extraPerson * packageData.extra_person_price;

  // Final amount
  var finalAmount = baseAmount + extraAmount;

  // Update display
  $('#base_amount').text(baseAmount.toFixed(2));
  $('#extra_person_amount').text(extraAmount.toFixed(2));
  $('#final_amount').text(finalAmount.toFixed(2));
}

function resetCalculations(){
  $('#base_amount').text('0.00');
  $('#extra_person_amount').text('0.00');
  $('#final_amount').text('0.00');
}
</script>
@endsection
