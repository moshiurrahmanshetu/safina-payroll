@extends('layouts.admin')
@section('title', 'Availability Calendar')
@section('content')
<h3 class="page-header">Availability Calendar</h3>

<!-- Availability Filter Form -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="nav-icon icon-search"></i> Check Availability
      </div>
      <div class="panel-body">
        <div class="row">
          <!-- Category Filter -->
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Category</label>
              <select name="category_id" id="category_id" class="form-control" onchange="loadServicesByCategory(this.value)">
                <option value="">Select Category</option>
                @foreach($service_categories as $id => $name)
                  <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Service Filter (Dependent) -->
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Service *</label>
              <select name="filter_service_id" id="filter_service_id" class="form-control" disabled>
                <option value="">Select Category First</option>
              </select>
            </div>
          </div>

          <!-- Check-in Date -->
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Check-in Date *</label>
              <input type="text" name="filter_check_in" id="filter_check_in" class="form-control datepicker" placeholder="DD-MM-YYYY" autocomplete="off">
            </div>
          </div>

          <!-- Check-out Date -->
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Check-out Date *</label>
              <input type="text" name="filter_check_out" id="filter_check_out" class="form-control datepicker" placeholder="DD-MM-YYYY" autocomplete="off">
            </div>
          </div>

          <!-- Time (Optional) -->
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Time (Optional)</label>
              <input type="time" name="filter_time" id="filter_time" class="form-control">
            </div>
          </div>

          <!-- Check Button -->
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">&nbsp;</label>
              <button type="button" class="btn btn-primary btn-block" onclick="checkAvailability()">
                <i class="nav-icon icon-search"></i> Check
              </button>
            </div>
          </div>
        </div>

        <!-- Availability Result Display -->
        <div id="availability_result" style="display:none;">
          <div class="alert" id="availability_alert">
            <div class="row">
              <div class="col-md-6">
                <h4 id="availability_title" class="mt-0 mb-1"></h4>
                <p id="availability_message" class="mb-0"></p>
              </div>
              <div class="col-md-6 text-right">
                <span id="capacity_display" class="label label-info"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div id="loading_state" style="display:none;">
          <div class="alert alert-info">
            <i class="nav-icon icon-refresh"></i> Checking availability...
          </div>
        </div>

        <!-- Error State -->
        <div id="error_state" style="display:none;">
          <div class="alert alert-danger">
            <i class="nav-icon icon-exclamation"></i> <span id="error_message">Error checking availability</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Calendar View -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-md-8">
            <!-- <strong>Calendar View</strong>
            <span class="label" style="background-color: #28a745;">Available</span>
            <span class="label" style="background-color: #ffc107; color: #000;">Limited</span>
            <span class="label" style="background-color: #dc3545;">Fully Booked</span> -->
          </div>
          <div class="col-md-4 text-right">
            <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">Create Booking</a>
            <a href="{{ route('bookings.index') }}" class="btn btn-success btn-sm">Booking List</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div id="calendar"></div>
        <div id="calendar_info" class="mt-3" style="display:none;">
          <div class="alert alert-info">
            <strong>Selected Date:</strong> <span id="calendar_selected_date"></span><br>
            <strong>Capacity:</strong> <span id="calendar_capacity"></span><br>
            <strong>Booked:</strong> <span id="calendar_booked"></span><br>
            <strong>Available:</strong> <span id="calendar_available"></span><br>
            <strong>Status:</strong> <span id="calendar_status"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.5/fullcalendar.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.5/fullcalendar.min.css">

<script>
$(document).ready(function() {
  let calendar = $('#calendar');
  let selectedServiceId = '';

  // Initialize datepickers
  $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    todayHighlight: true
  });

  // Initialize calendar
  calendar.fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,basicWeek,basicDay'
    },
    defaultDate: moment(),
    editable: false,
    eventLimit: true,
    events: [],
    dayClick: function(date, jsEvent, view) {
      if(!selectedServiceId) {
        alert('Please select a service first from the filter above');
        return;
      }
      checkCalendarAvailability(date.format('DD-MM-YYYY'));
    }
  });

  // Load calendar data when service is selected from filter
  $('#filter_service_id').on('change', function() {
    selectedServiceId = $(this).val();
    if(selectedServiceId) {
      loadCalendarData(selectedServiceId, calendar.fullCalendar('getDate').month() + 1, calendar.fullCalendar('getDate').year());
    } else {
      calendar.fullCalendar('removeEvents');
      $('#calendar_info').hide();
    }
  });
});

// Load services by category
function loadServicesByCategory(category_id) {
  var serviceSelect = document.getElementById('filter_service_id');

  if(!category_id){
    serviceSelect.innerHTML = '<option value="">Select Category First</option>';
    serviceSelect.disabled = true;
    return;
  }

  // Show loading
  serviceSelect.innerHTML = '<option value="">Loading...</option>';
  serviceSelect.disabled = true;

  // Fetch services via AJAX
  $.ajax({
    url: "{{ route('get.services', ':category_id') }}".replace(':category_id', category_id),
    type: 'GET',
    dataType: 'json',
    success: function(response){
      if(response.status === true){
        var options = '<option value="">Select Service</option>';

        if(response.data.length === 0){
          options = '<option value="">No services available</option>';
        } else {
          for(var i = 0; i < response.data.length; i++){
            var service = response.data[i];
            options += '<option value="' + service.id + '">' + service.name + '</option>';
          }
          serviceSelect.disabled = false;
        }

        serviceSelect.innerHTML = options;
      } else {
        serviceSelect.innerHTML = '<option value="">' + (response.message || 'Error loading services') + '</option>';
      }
    },
    error: function(xhr, status, error){
      console.error('Error loading services:', error);
      serviceSelect.innerHTML = '<option value="">Error loading services</option>';
    }
  });
}

// Check availability with date range
function checkAvailability() {
  var serviceId = $('#filter_service_id').val();
  var checkIn = $('#filter_check_in').val();
  var checkOut = $('#filter_check_out').val();

  // Validation
  if(!serviceId){
    alert('Please select a service');
    return;
  }
  if(!checkIn){
    alert('Please select check-in date');
    return;
  }
  if(!checkOut){
    alert('Please select check-out date');
    return;
  }

  // Hide previous results and show loading
  $('#availability_result').hide();
  $('#error_state').hide();
  $('#loading_state').show();

  // AJAX call to check availability
  $.ajax({
    url: '{{ route("bookings.availability.check") }}',
    type: 'GET',
    dataType: 'json',
    data: {
      service_id: serviceId,
      check_in_date: checkIn,
      check_out_date: checkOut
    },
    success: function(response){
      $('#loading_state').hide();

      if(response.success){
        var resultDiv = $('#availability_result');
        var alertDiv = $('#availability_alert');
        var title = $('#availability_title');
        var message = $('#availability_message');
        var capacityDisplay = $('#capacity_display');

        if(response.available > 0){
          // Available
          alertDiv.removeClass('alert-danger alert-warning').addClass('alert-success');
          title.html('<i class="nav-icon icon-check"></i> Available Slots: ' + response.available);
          message.html('Service: <strong>' + response.service_name + '</strong><br>' +
                       'Total Capacity: ' + response.total_capacity + ' | Already Booked: ' + response.booked);
          capacityDisplay.removeClass('label-danger label-warning').addClass('label-success');
        } else {
          // Fully Booked
          alertDiv.removeClass('alert-success alert-warning').addClass('alert-danger');
          title.html('<i class="nav-icon icon-close"></i> Fully Booked');
          message.html('Service: <strong>' + response.service_name + '</strong><br>' +
                       'No slots available for the selected dates.');
          capacityDisplay.removeClass('label-success label-warning').addClass('label-danger');
        }

        capacityDisplay.text(response.message);
        resultDiv.show();
      } else {
        $('#error_message').text(response.message || 'Error checking availability');
        $('#error_state').show();
      }
    },
    error: function(xhr, status, error){
      $('#loading_state').hide();
      $('#error_message').text('Error connecting to server. Please try again.');
      $('#error_state').show();
      console.error('Availability check error:', error);
    }
  });
}

// Calendar availability check
function checkCalendarAvailability(date) {
  var serviceId = $('#filter_service_id').val() || selectedServiceId;

  if(!serviceId){
    return;
  }

  $.ajax({
    url: '{{ route("bookings.availability.check") }}',
    type: 'GET',
    data: {
      service_id: serviceId,
      date: date
    },
    success: function(response) {
      if(response.success) {
        $('#calendar_selected_date').text(date);
        $('#calendar_capacity').text(response.total_capacity || response.capacity);
        $('#calendar_booked').text(response.booked);
        $('#calendar_available').text(response.available);
        $('#calendar_status').html('<span class="label" style="background-color: ' + getStatusColor(response.status) + '">' + response.message + '</span>');
        $('#calendar_info').show();
      }
    },
    error: function(xhr, status, error) {
      console.error('Error checking availability:', error);
    }
  });
}

function loadCalendarData(serviceId, month, year) {
  $.ajax({
    url: '{{ route("bookings.availability.get_calendar_data") }}',
    type: 'GET',
    data: {
      service_id: serviceId,
      month: month,
      year: year
    },
    success: function(response) {
      if(response.success) {
        $('#calendar').fullCalendar('removeEvents');

        let events = response.events.map(function(event) {
          return {
            title: event.title,
            start: event.date,
            color: event.color,
            textColor: event.status === 'limited' ? '#000' : '#fff',
            allDay: true
          };
        });

        $('#calendar').fullCalendar('addEventSource', events);
      }
    },
    error: function(xhr, status, error) {
      console.error('Error loading calendar data:', error);
    }
  });
}

function getStatusColor(status) {
  switch(status) {
    case 'available': return '#28a745';
    case 'limited': return '#ffc107';
    case 'fully_booked': return '#dc3545';
    default: return '#6c757d';
  }
}
</script>
@endsection
