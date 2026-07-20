@extends('layouts.admin')
@section('title', 'Attendance Month Edit')
@section('content')
<h3 class="page-header">Attendance Month Edit {{link_to_route('attendances.index','Attendance List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model($attendanceMonth,array('route' => array('attendances.update', $attendanceMonth->id),'enctype'=>'multipart/form-data','method'=>'PUT','class'=>'form-horizontal','id'=>'attendanceForm')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Attendance Month Details</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Employee *</label>
              <input type="text" class="form-control" value="{{ $attendanceMonth->user ? $attendanceMonth->user->name : 'N/A' }}" readonly>
              {{ Form::hidden('user_id', $attendanceMonth->user_id) }}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Attendance Month *</label>
              <input type="month" name="attendance_month" class="form-control" value="{{ $attendanceMonth->attendance_month }}" readonly>
              {{ Form::hidden('attendance_month', $attendanceMonth->attendance_month) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Attendance Summary -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Attendance Summary (Live)</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Total Days</label>
              <input type="text" class="form-control" id="summary_total_days" value="{{ date('t', strtotime($attendanceMonth->attendance_month . '-01')) }}" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Working Days</label>
              <input type="text" class="form-control" id="summary_working_days" value="0" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Present</label>
              <input type="text" class="form-control" id="summary_present" value="{{ $attendanceMonth->summary_present }}" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Late</label>
              <input type="text" class="form-control" id="summary_late" value="{{ $attendanceMonth->summary_late }}" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Half Day</label>
              <input type="text" class="form-control" id="summary_halfday" value="{{ $attendanceMonth->summary_halfday }}" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Absent</label>
              <input type="text" class="form-control" id="summary_absent" value="{{ $attendanceMonth->summary_absent }}" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Leave</label>
              <input type="text" class="form-control" id="summary_leave" value="{{ $attendanceMonth->summary_leave }}" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Holiday</label>
              <input type="text" class="form-control" id="summary_holiday" value="{{ $attendanceMonth->summary_holiday }}" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Weekly Off</label>
              <input type="text" class="form-control" id="summary_weekly_off" value="{{ $attendanceMonth->summary_weekly_off }}" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Payable Days</label>
              <input type="text" class="form-control" id="summary_payable_days" value="0" readonly>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Lock Badge -->
@if($attendanceMonth->attendance_locked)
<div class="row">
  <div class="col-md-12">
    <div class="alert alert-danger">
      <strong><i class="nav-icon icon-lock"></i> Attendance Locked</strong> - This attendance month is locked and cannot be edited.
    </div>
  </div>
</div>
@endif

<!-- Bulk Actions -->
@if(!$attendanceMonth->attendance_locked)
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Bulk Actions</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-2">
            <button type="button" class="btn btn-success form-control" id="markAllPresent">
              <i class="nav-icon icon-check"></i> Mark All Present
            </button>
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-danger form-control" id="markAllAbsent">
              <i class="nav-icon icon-close"></i> Mark All Absent
            </button>
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-warning form-control" id="markAllLeave">
              <i class="nav-icon icon-clock"></i> Mark All Leave
            </button>
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-info form-control" id="markAllHoliday">
              <i class="nav-icon icon-calendar"></i> Mark All Holiday
            </button>
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-secondary form-control" id="markAllWeeklyOff">
              <i class="nav-icon icon-refresh"></i> Mark All Weekly Off
            </button>
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-dark form-control" id="clearMonth">
              <i class="nav-icon icon-trash"></i> Clear Month
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Navigation -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <a href="{{ route('attendances.edit', ['attendance' => $attendanceMonth->id,'month' => date('Y-m', strtotime($attendanceMonth->attendance_month . '-01 -1 month')) ]) }}" class="btn btn-primary">
              <i class="nav-icon icon-arrow-left"></i> Previous Month
            </a>
          </div>
          <div class="col-md-6 text-right">
            <a href="{{ route('attendances.edit', ['attendance' => $attendanceMonth->id,'month' => date('Y-m', strtotime($attendanceMonth->attendance_month . '-01 +1 month')) ]) }}" class="btn btn-primary">
              Next Month <i class="nav-icon icon-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Attendance Editor Table -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Daily Attendance Editor</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="attendanceTable">
            <thead>
              <tr>
                <th style="width: 80px;">Date</th>
                <th style="width: 120px;">Day</th>
                <th style="width: 100px;">Check In</th>
                <th style="width: 100px;">Check Out</th>
                <th style="width: 150px;">Status</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody id="attendanceTableBody">
              @php
                $attendanceJson = $attendanceMonth->attendance_json ?? [];
                $daysInMonth = date('t', strtotime($attendanceMonth->attendance_month . '-01'));
              @endphp
              @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                  $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
                  $dayData = $attendanceJson[$dayKey] ?? ['status' => '', 'check_in' => '', 'check_out' => '', 'remarks' => ''];
                  $date = new \DateTime($attendanceMonth->attendance_month . '-' . $dayKey);
                  $dayName = $date->format('l');
                  $dayOfWeek = $date->format('w'); // 0 = Sunday, 6 = Saturday
                  $isLocked = $attendanceMonth->attendance_locked;
                  
                  // Auto-detect weekends if no existing data
                  if (!isset($attendanceJson[$dayKey]) && ($dayOfWeek == 0 || $dayOfWeek == 6)) {
                    $dayData['status'] = 'Weekly Off';
                  }
                @endphp
                <tr data-day="{{ $dayKey }}">
                  <td><strong>{{ $dayKey }}</strong></td>
                  <td>{{ $dayName }}</td>
                  <td>
                    <input type="time" 
                           class="form-control check-in" 
                           name="attendance_json[{{ $dayKey }}][check_in]" 
                           value="{{ $dayData['check_in'] }}" 
                           {{ $isLocked ? 'disabled' : '' }}>
                  </td>
                  <td>
                    <input type="time" 
                           class="form-control check-out" 
                           name="attendance_json[{{ $dayKey }}][check_out]" 
                           value="{{ $dayData['check_out'] }}" 
                           {{ $isLocked ? 'disabled' : '' }}>
                  </td>
                  <td>
                    <select class="form-control status" 
                            name="attendance_json[{{ $dayKey }}][status]" 
                            {{ $isLocked ? 'disabled' : '' }}>
                      <option value="">-</option>
                      @foreach(config('myhelpers.attendance_status') as $key => $value)
                        <option value="{{ $key }}" {{ $dayData['status'] == $key ? 'selected' : '' }}>{{ $value }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <input type="text" 
                           class="form-control remarks" 
                           name="attendance_json[{{ $dayKey }}][remarks]" 
                           value="{{ $dayData['remarks'] }}" 
                           {{ $isLocked ? 'disabled' : '' }}>
                  </td>
                </tr>
              @endfor
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Hidden field for attendance_json -->
<input type="hidden" name="attendance_json" id="attendance_json" value="">

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      @if(!$attendanceMonth->attendance_locked)
        <button type="submit" class="btn btn-primary" id="saveBtn">
          <i class="nav-icon icon-save"></i> Save Whole Month
        </button>
      @endif
      {!! HTML::decode(link_to_route('attendances.index', '<i class="nav-icon icon-arrow-left"></i> Back', [], array('class' => 'btn btn-danger'))) !!}
    </div>
  </div>
</div>
{{ Form::close() }}
</div>

@endsection
@section('script')
<script>
$(document).ready(function() {
  const isLocked = {{ $attendanceMonth->attendance_locked ? 'true' : 'false' }};

  // Add event listeners for live summary (only if not locked)
  if (!isLocked) {
    $('.status').on('change', function() {
      validateCheckInOut($(this));
      calculateSummary();
    });
    $('.check-in, .check-out').on('change', function() {
      calculateSummary();
    });
  }

  // Validate check-in/out based on status
  function validateCheckInOut(statusSelect) {
    const row = statusSelect.closest('tr');
    const status = statusSelect.val();
    const checkIn = row.find('.check-in');
    const checkOut = row.find('.check-out');

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
  }

  // Validate check-out not earlier than check-in
  function validateCheckOutTime(checkOutInput) {
    const row = checkOutInput.closest('tr');
    const checkIn = row.find('.check-in').val();
    const checkOut = checkOutInput.val();

    if (checkIn && checkOut) {
      if (checkOut <= checkIn) {
        alert('Check-out time must be later than check-in time');
        checkOutInput.val('');
        return false;
      }
    }
    return true;
  }

  // Bulk action: Mark all as Present
  $('#markAllPresent').click(function() {
    $('.status').val('Present');
    $('.status').each(function() {
      validateCheckInOut($(this));
    });
    calculateSummary();
  });

  // Bulk action: Mark all as Absent
  $('#markAllAbsent').click(function() {
    $('.status').val('Absent');
    $('.status').each(function() {
      validateCheckInOut($(this));
    });
    calculateSummary();
  });

  // Bulk action: Mark all as Leave
  $('#markAllLeave').click(function() {
    $('.status').val('Leave');
    $('.status').each(function() {
      validateCheckInOut($(this));
    });
    calculateSummary();
  });

  // Bulk action: Mark all as Holiday
  $('#markAllHoliday').click(function() {
    $('.status').val('Holiday');
    $('.status').each(function() {
      validateCheckInOut($(this));
    });
    calculateSummary();
  });

  // Bulk action: Mark all as Weekly Off
  $('#markAllWeeklyOff').click(function() {
    $('.status').val('Weekly Off');
    $('.status').each(function() {
      validateCheckInOut($(this));
    });
    calculateSummary();
  });

  // Bulk action: Clear month
  $('#clearMonth').click(function() {
    if (confirm('Are you sure you want to clear all attendance data for this month?')) {
      $('.status').val('');
      $('.check-in').val('');
      $('.check-out').val('');
      $('.remarks').val('');
      calculateSummary();
    }
  });

  // Add event listener for check-out validation
  $(document).on('change', '.check-out', function() {
    validateCheckOutTime($(this));
  });

  // Calculate live summary
  function calculateSummary() {
    let summary = {
      Present: 0,
      Late: 0,
      'Half Day': 0,
      Absent: 0,
      Leave: 0,
      Holiday: 0,
      'Weekly Off': 0
    };

    $('.status').each(function() {
      const status = $(this).val();
      if (summary.hasOwnProperty(status)) {
        summary[status]++;
      }
    });

    const totalDays = parseInt($('#summary_total_days').val());
    
    // Calculate working days (Total - Holiday - Weekly Off)
    const workingDays = totalDays - summary.Holiday - summary['Weekly Off'];
    $('#summary_working_days').val(workingDays);

    $('#summary_present').val(summary.Present);
    $('#summary_late').val(summary.Late);
    $('#summary_halfday').val(summary['Half Day']);
    $('#summary_absent').val(summary.Absent);
    $('#summary_leave').val(summary.Leave);
    $('#summary_holiday').val(summary.Holiday);
    $('#summary_weekly_off').val(summary['Weekly Off']);

    // Calculate payable days (Present + Late + Half Day + Leave)
    const payableDays = summary.Present + summary.Late + (summary['Half Day'] * 0.5) + summary.Leave;
    $('#summary_payable_days').val(payableDays);
  }

  // Form submit
  $('#attendanceForm').submit(function(e) {
    if (isLocked) {
      e.preventDefault();
      alert('Cannot save locked attendance');
      return false;
    }

    // Build attendance JSON from form
    const formData = $(this).serializeArray();
    let attendanceData = {};

    formData.forEach(function(item) {
      if (item.name.startsWith('attendance_json[')) {
        const matches = item.name.match(/attendance_json\[(\d+)\]\[(\w+)\]/);
        if (matches) {
          const day = matches[1];
          const field = matches[2];
          
          if (!attendanceData[day]) {
            attendanceData[day] = {};
          }
          attendanceData[day][field] = item.value;
        }
      }
    });

    // Set JSON to hidden field
    $('#attendance_json').val(JSON.stringify(attendanceData));
  });
});
</script>
@endsection
