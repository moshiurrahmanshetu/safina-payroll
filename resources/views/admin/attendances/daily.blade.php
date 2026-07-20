@extends('layouts.admin')
@section('title', 'Daily Attendance Entry')
@section('content')
<h3 class="page-header">Daily Attendance Entry {{link_to_route('attendances.index','Attendance List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::open(array('route' => 'attendances.daily_store','enctype'=>'multipart/form-data','class'=>'form-horizontal','id'=>'dailyAttendanceForm')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Attendance Details</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Employee *</label>
              <select name="user_id" class="form-control" id="user_id" required>
                <option value="">Select Employee</option>
                @if($users->count() > 0)
                  @foreach($users as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                  @endforeach
                @else
                  <option value="" disabled>No eligible employees found (users with salary_processing=1 and status=Active)</option>
                @endif
              </select>
              {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Month *</label>
              <input type="month" name="attendance_month" class="form-control" id="attendance_month" required>
              {!! $errors->first('attendance_month', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Date *</label>
              <input type="date" name="attendance_date" class="form-control" id="attendance_date" required>
              {!! $errors->first('attendance_date', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">&nbsp;</label>
              <button type="button" class="btn btn-primary form-control" id="loadDayBtn">
                <i class="nav-icon icon-refresh"></i> Load Day
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Attendance Form (Hidden initially) -->
<div class="row" id="attendanceFormSection" style="display: none;">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Daily Attendance</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Employee</label>
              <input type="text" class="form-control" id="employee_name" value="" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Day</label>
              <input type="text" class="form-control" id="day_name" value="" readonly>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Check In</label>
              <input type="time" name="check_in" class="form-control" id="check_in" value="">
              {!! $errors->first('check_in', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Check Out</label>
              <input type="time" name="check_out" class="form-control" id="check_out" value="">
              {!! $errors->first('check_out', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Status *</label>
              <select name="status" class="form-control" id="status" required>
                <option value="">-</option>
                @foreach(config('myhelpers.attendance_status') as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </select>
              {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Remarks</label>
              <input type="text" name="remarks" class="form-control" id="remarks" value="">
              {!! $errors->first('remarks', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Lock Badge (Hidden initially) -->
<div class="row" id="lockSection" style="display: none;">
  <div class="col-md-12">
    <div class="alert alert-danger">
      <strong><i class="nav-icon icon-lock"></i> Attendance Locked</strong> - This attendance month is locked and cannot be edited.
    </div>
  </div>
</div>

<div class="row" id="saveSection" style="display: none;">
  <div class="col-md-12">
    <div class="form-group">
      <button type="submit" class="btn btn-primary" id="saveBtn">
        <i class="nav-icon icon-save"></i> Save Daily Attendance
      </button>
      <a href="{{ route('attendances.daily') }}" class="btn btn-danger">
        <i class="nav-icon icon-close"></i> Cancel
      </a>
    </div>
  </div>
</div>
{{ Form::close() }}
</div>

@endsection
@section('script')
<script>
$(document).ready(function() {
  let isLocked = false;

  // Set default date to today
  const today = new Date().toISOString().split('T')[0];
  $('#attendance_date').val(today);

  // Set default month to current month
  const currentMonth = new Date().toISOString().slice(0, 7);
  $('#attendance_month').val(currentMonth);

  // Load day button click
  $('#loadDayBtn').click(function() {
    const userId = $('#user_id').val();
    const attendanceMonth = $('#attendance_month').val();
    const attendanceDate = $('#attendance_date').val();

    if (!userId || !attendanceMonth || !attendanceDate) {
      alert('Please select employee, month, and date');
      return;
    }

    // Show loading state
    $('#loadDayBtn').html('<i class="nav-icon icon-refresh"></i> Loading...').prop('disabled', true);

    // AJAX call to load day
    $.ajax({
      url: '{{ route("attendances.load_day") }}',
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        user_id: userId,
        attendance_month: attendanceMonth,
        attendance_date: attendanceDate
      },
      success: function(response) {
        isLocked = response.attendance_locked;

        // Show employee name
        $('#employee_name').val(response.employee_name);

        // Show day name
        const date = new Date(attendanceDate);
        const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
        $('#day_name').val(dayName);

        // Load existing data if exists
        if (response.day_data) {
          $('#check_in').val(response.day_data.check_in || '');
          $('#check_out').val(response.day_data.check_out || '');
          $('#status').val(response.day_data.status || '');
          $('#remarks').val(response.day_data.remarks || '');
        } else {
          // Clear form for new entry
          $('#check_in').val('');
          $('#check_out').val('');
          $('#status').val('');
          $('#remarks').val('');
          
          // Auto-detect weekend
          const dayOfWeek = date.getDay(); // 0 = Sunday, 6 = Saturday
          if (dayOfWeek === 0 || dayOfWeek === 6) {
            $('#status').val('Weekly Off');
          }
        }

        // Show attendance form section
        $('#attendanceFormSection').show();

        // Show lock section if locked
        if (isLocked) {
          $('#lockSection').show();
          $('#saveSection').hide();
          $('#check_in').prop('disabled', true);
          $('#check_out').prop('disabled', true);
          $('#status').prop('disabled', true);
          $('#remarks').prop('disabled', true);
        } else {
          $('#lockSection').hide();
          $('#saveSection').show();
          $('#check_in').prop('disabled', false);
          $('#check_out').prop('disabled', false);
          $('#status').prop('disabled', false);
          $('#remarks').prop('disabled', false);
        }

        // Reset button
        $('#loadDayBtn').html('<i class="nav-icon icon-refresh"></i> Load Day').prop('disabled', false);
      },
      error: function(xhr) {
        alert('Error loading day: ' + xhr.responseJSON.error);
        $('#loadDayBtn').html('<i class="nav-icon icon-refresh"></i> Load Day').prop('disabled', false);
      }
    });
  });

  // Validate check-in/out based on status
  $('#status').on('change', function() {
    const status = $(this).val();
    const checkIn = $('#check_in');
    const checkOut = $('#check_out');

    const statusesRequiringTime = ['Present', 'Late', 'Half Day'];
    const statusesNotRequiringTime = ['Holiday', 'Weekly Off', 'Leave', 'Absent', ''];

    if (statusesRequiringTime.includes(status)) {
      checkIn.prop('required', true);
      checkIn.prop('disabled', false);
      checkOut.prop('required', true);
      checkOut.prop('disabled', false);
    } else if (statusesNotRequiringTime.includes(status)) {
      checkIn.prop('required', false);
      checkIn.prop('disabled', true);
      checkOut.prop('required', false);
      checkOut.prop('disabled', true);
      checkIn.val('');
      checkOut.val('');
    }
  });

  // Validate check-out not earlier than check-in
  $('#check_out').on('change', function() {
    const checkIn = $('#check_in').val();
    const checkOut = $(this).val();

    if (checkIn && checkOut) {
      if (checkOut <= checkIn) {
        alert('Check-out time must be later than check-in time');
        $(this).val('');
        return false;
      }
    }
    return true;
  });

  // Form submit
  $('#dailyAttendanceForm').submit(function(e) {
    if (isLocked) {
      e.preventDefault();
      alert('Cannot save locked attendance');
      return false;
    }

    const status = $('#status').val();
    const checkIn = $('#check_in').val();
    const checkOut = $('#check_out').val();

    const statusesRequiringTime = ['Present', 'Late', 'Half Day'];
    if (statusesRequiringTime.includes(status)) {
      if (!checkIn || !checkOut) {
        e.preventDefault();
        alert('Check-in and Check-out are required for this status');
        return false;
      }
    }
  });
});
</script>
@endsection
