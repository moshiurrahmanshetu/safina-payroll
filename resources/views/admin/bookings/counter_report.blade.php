@extends('layouts.admin')
@section('title', 'Counter Report')
@section('content')
<h3 class="page-header">Counter Report</h3>

<div class="panel panel-default">
  <div class="panel-heading">Filter Options</div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Start Date</label>
          <input type="text" id="start_date" class="form-control datepicker" placeholder="DD-MM-YYYY" value="{{ date('d-m-Y') }}">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>End Date</label>
          <input type="text" id="end_date" class="form-control datepicker" placeholder="DD-MM-YYYY" value="{{ date('d-m-Y') }}">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>Counter</label>
          <select id="counter_id" class="form-control">
            <option value="">All Counters</option>
            @foreach($counters as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>User</label>
          <select id="user_id" class="form-control">
            <option value="">All Users</option>
            @foreach($users as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button type="button" class="btn btn-primary" onclick="loadReport()">Generate Report</button>
        <button type="button" class="btn btn-success" onclick="printReport()">Print</button>
      </div>
    </div>
  </div>
</div>

<div id="report_results" style="display:none;">
  <div class="row">
    <div class="col-md-4">
      <div class="panel panel-info">
        <div class="panel-heading">Total Bookings</div>
        <div class="panel-body text-center">
          <h2 id="total_bookings">0</h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="panel panel-success">
        <div class="panel-heading">Total Amount</div>
        <div class="panel-body text-center">
          <h2 id="total_amount">৳0.00</h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="panel panel-warning">
        <div class="panel-heading">Average per Booking</div>
        <div class="panel-body text-center">
          <h2 id="average_amount">৳0.00</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">Service-wise Breakdown</div>
        <div class="panel-body">
          <table class="table table-bordered" id="service_breakdown">
            <thead>
              <tr><th>Service</th><th>Bookings</th><th>Amount</th></tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">User-wise Breakdown</div>
        <div class="panel-body">
          <table class="table table-bordered" id="user_breakdown">
            <thead>
              <tr><th>User</th><th>Bookings</th><th>Amount</th></tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">Counter-wise Breakdown</div>
        <div class="panel-body">
          <table class="table table-bordered" id="counter_breakdown">
            <thead>
              <tr><th>Counter</th><th>Bookings</th><th>Amount</th></tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">Booking Details</div>
    <div class="panel-body">
      <table class="table table-bordered table-striped" id="booking_details">
        <thead>
          <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Service</th>
            <th>Customer</th>
            <th>Counter</th>
            <th>User</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

@section('script')
<script>
$(document).ready(function(){
  // Initialize datepickers
  $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    todayHighlight: true
  });
});

function loadReport(){
  var startDate = $('#start_date').val();
  var endDate = $('#end_date').val();
  var counterId = $('#counter_id').val();
  var userId = $('#user_id').val();

  if(!startDate || !endDate){
    alert('Please select start and end dates');
    return;
  }

  $.ajax({
    url: '{{ route("bookings.counter_report.data") }}',
    type: 'GET',
    data: {
      start_date: startDate,
      end_date: endDate,
      counter_id: counterId,
      user_id: userId
    },
    success: function(response){
      if(response.success){
        displayReport(response);
      } else {
        alert(response.message || 'Error loading report');
      }
    },
    error: function(){
      alert('Error loading report data');
    }
  });
}

function displayReport(data){
  $('#report_results').show();

  // Update summary
  $('#total_bookings').text(data.total_bookings);
  $('#total_amount').text('৳' + parseFloat(data.total_amount).toFixed(2));
  var avg = data.total_bookings > 0 ? data.total_amount / data.total_bookings : 0;
  $('#average_amount').text('৳' + avg.toFixed(2));

  // Service breakdown
  var serviceHtml = '';
  for(var service in data.service_breakdown){
    var item = data.service_breakdown[service];
    serviceHtml += '<tr><td>' + service + '</td><td>' + item.count + '</td><td>৳' + parseFloat(item.amount).toFixed(2) + '</td></tr>';
  }
  $('#service_breakdown tbody').html(serviceHtml || '<tr><td colspan="3" class="text-center">No data</td></tr>');

  // User breakdown
  var userHtml = '';
  for(var user in data.user_breakdown){
    var item = data.user_breakdown[user];
    userHtml += '<tr><td>' + user + '</td><td>' + item.count + '</td><td>৳' + parseFloat(item.amount).toFixed(2) + '</td></tr>';
  }
  $('#user_breakdown tbody').html(userHtml || '<tr><td colspan="3" class="text-center">No data</td></tr>');

  // Counter breakdown
  var counterHtml = '';
  for(var counter in data.counter_breakdown){
    var item = data.counter_breakdown[counter];
    counterHtml += '<tr><td>' + counter + '</td><td>' + item.count + '</td><td>৳' + parseFloat(item.amount).toFixed(2) + '</td></tr>';
  }
  $('#counter_breakdown tbody').html(counterHtml || '<tr><td colspan="3" class="text-center">No data</td></tr>');

  // Booking details
  var detailsHtml = '';
  data.bookings.forEach(function(booking){
    detailsHtml += '<tr>';
    detailsHtml += '<td>' + booking.id + '</td>';
    detailsHtml += '<td>' + booking.check_in_date + '</td>';
    detailsHtml += '<td>' + (booking.service ? booking.service.name : '-') + '</td>';
    detailsHtml += '<td>' + booking.name + '</td>';
    detailsHtml += '<td>' + (booking.counter ? booking.counter.name : '-') + '</td>';
    detailsHtml += '<td>' + (booking.creator ? booking.creator.name : '-') + '</td>';
    detailsHtml += '<td>৳' + parseFloat(booking.final_price).toFixed(2) + '</td>';
    detailsHtml += '</tr>';
  });
  $('#booking_details tbody').html(detailsHtml || '<tr><td colspan="7" class="text-center">No bookings found</td></tr>');
}

function printReport(){
  window.print();
}
</script>
@endsection
@endsection
