@extends('layouts.admin')
@section('title', 'Availability Calendar')
@section('content')
<h3 class="page-header">Availability Calendar</h3>

<!-- Occupancy Summary -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title">Occupancy Summary (Today)</h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$totalServices}}</h4>
              <small>Total Services</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$bookingsToday}}</h4>
              <small>Booked Today</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$availableToday}}</h4>
              <small>Available Today</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$occupancyPercent}}%</h4>
              <small>Occupancy</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Month Navigation -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <a href="{{ url('/admin/availability?year='.$previousMonth->year.'&month='.$previousMonth->month) }}" class="btn btn-default">
              <i class="nav-icon icon-arrow-left"></i> Previous Month
            </a>
          </div>
          <div class="col-md-4 text-center">
            <h4>{{$currentDate->format('F Y')}}</h4>
          </div>
          <div class="col-md-4 text-right">
            <a href="{{ url('/admin/availability?year='.$nextMonth->year.'&month='.$nextMonth->month) }}" class="btn btn-default">
              Next Month <i class="nav-icon icon-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Availability Grid -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Service Availability</h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed">
            <thead>
              <tr>
                <th style="min-width: 200px;">Service</th>
                @for($day = 1; $day <= $daysInMonth; $day++)
                  <th class="text-center" style="min-width: 40px;">{{$day}}</th>
                @endfor
              </tr>
            </thead>
            <tbody>
              @foreach($availabilityGrid as $serviceData)
              <tr>
                <td>
                  <strong>{{$serviceData['service']->name}}</strong>
                  <br>
                  <small class="text-muted">{{$serviceData['service']->service_category ? $serviceData['service']->service_category->name : 'N/A'}}</small>
                </td>
                @for($day = 1; $day <= $daysInMonth; $day++)
                  @php
                    $dayData = $serviceData['days'][$day];
                    $date = Carbon::create($currentDate->year, $currentDate->month, $day)->format('Y-m-d');
                  @endphp
                  <td class="text-center" style="padding: 5px;">
                    @if($dayData['status'] === 'booked')
                      <span class="label label-danger availability-cell"
                            data-service-id="{{$serviceData['service']->id}}"
                            data-date="{{$date}}"
                            style="cursor: pointer; width: 100%; display: block;">
                        ✕
                      </span>
                    @else
                      <span class="label label-success availability-cell"
                            style="cursor: default; width: 100%; display: block;">
                        ✓
                      </span>
                    @endif
                  </td>
                @endfor
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Booking Details</h4>
      </div>
      <div class="modal-body" id="bookingDetailsContent">
        <div class="text-center">
          <i class="nav-icon icon-refresh spin"></i> Loading...
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
  // Handle click on booked cells
  $('.availability-cell[data-service-id]').on('click', function() {
    var serviceId = $(this).data('service-id');
    var date = $(this).data('date');

    $('#bookingDetailsModal').modal('show');
    $('#bookingDetailsContent').html('<div class="text-center"><i class="nav-icon icon-refresh spin"></i> Loading...</div>');

    $.ajax({
      url: "{{ url('/admin/availability/booking-details') }}",
      type: 'GET',
      data: {
        service_id: serviceId,
        date: date
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === true) {
          var html = '<table class="table table-bordered">' +
            '<tr><td><strong>Booking ID:</strong></td><td>#' + response.data.id + '</td></tr>' +
            '<tr><td><strong>Customer Name:</strong></td><td>' + response.data.customer_name + '</td></tr>' +
            '<tr><td><strong>Phone:</strong></td><td>' + (response.data.phone || 'N/A') + '</td></tr>' +
            '<tr><td><strong>Service:</strong></td><td>' + response.data.service_name + '</td></tr>' +
            '<tr><td><strong>Check-in Date:</strong></td><td>' + response.data.check_in_date + '</td></tr>' +
            '<tr><td><strong>Check-in Time:</strong></td><td>' + (response.data.check_in_time || 'N/A') + '</td></tr>' +
            '<tr><td><strong>Check-out Date:</strong></td><td>' + response.data.check_out_date + '</td></tr>' +
            '<tr><td><strong>Check-out Time:</strong></td><td>' + (response.data.check_out_time || 'N/A') + '</td></tr>' +
            '<tr><td><strong>Time Slot:</strong></td><td>' + response.data.time_slot + '</td></tr>' +
            '<tr><td><strong>Status:</strong></td><td>' + response.data.status + '</td></tr>' +
            '<tr><td><strong>Final Price:</strong></td><td>৳' + response.data.final_price + '</td></tr>' +
            '</table>';
          $('#bookingDetailsContent').html(html);
        } else {
          $('#bookingDetailsContent').html('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr, status, error) {
        $('#bookingDetailsContent').html('<div class="alert alert-danger">Error loading booking details</div>');
      }
    });
  });
});
</script>
@endsection
