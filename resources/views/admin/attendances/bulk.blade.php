@extends('layouts.admin')
@section('title', 'Bulk Attendance Editor')
@section('content')
<h3 class="page-header">Bulk Attendance Editor {{link_to_route('attendances.index','Attendance List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::open(array('route' => array('attendances.bulk_store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal','id'=>'attendanceForm')) }}
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
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Attendance Month *</label>
              <input type="month" name="attendance_month" class="form-control" id="attendance_month" required>
              {!! $errors->first('attendance_month', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">&nbsp;</label>
              <button type="button" class="btn btn-primary form-control" id="loadMonthBtn">
                <i class="nav-icon icon-refresh"></i> Load Month
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Attendance Summary (Hidden initially) -->
<div class="row" id="summarySection" style="display: none;">
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
              <input type="text" class="form-control" id="summary_total_days" value="0" readonly>
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
              <input type="text" class="form-control" id="summary_present" value="0" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Late</label>
              <input type="text" class="form-control" id="summary_late" value="0" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Half Day</label>
              <input type="text" class="form-control" id="summary_halfday" value="0" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Absent</label>
              <input type="text" class="form-control" id="summary_absent" value="0" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Leave</label>
              <input type="text" class="form-control" id="summary_leave" value="0" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Holiday</label>
              <input type="text" class="form-control" id="summary_holiday" value="0" readonly>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label">Weekly Off</label>
              <input type="text" class="form-control" id="summary_weekly_off" value="0" readonly>
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

<!-- Bulk Actions (Hidden initially) -->
<div class="row" id="bulkActionsSection" style="display: none;">
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

<!-- Navigation (Hidden initially) -->
<div class="row" id="navigationSection" style="display: none;">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <button type="button" class="btn btn-primary" id="prevMonthBtn">
              <i class="nav-icon icon-arrow-left"></i> Previous Month
            </button>
          </div>
          <div class="col-md-6 text-right">
            <button type="button" class="btn btn-primary" id="nextMonthBtn">
              Next Month <i class="nav-icon icon-arrow-right"></i>
            </button>
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

<!-- Attendance Editor Table (Hidden initially) -->
<div class="row" id="editorSection" style="display: none;">
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
              <!-- Rows will be generated by JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Hidden field for attendance_json -->
<input type="hidden" name="attendance_json" id="attendance_json" value="">

<div class="row" id="saveSection" style="display: none;">
  <div class="col-md-12">
    <div class="form-group">
      <button type="submit" class="btn btn-primary" id="saveBtn">
        <i class="nav-icon icon-save"></i> Save Whole Month
      </button>
      <a href="{{ route('attendances.bulk') }}" class="btn btn-default">
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
  let attendanceData = {};
  let isLocked = false;

  // Load month button click
  $('#loadMonthBtn').click(function() {
    const userId = $('#user_id').val();
    const attendanceMonth = $('#attendance_month').val();

    if (!userId || !attendanceMonth) {
      alert('Please select employee and attendance month');
      return;
    }

    // Show loading state
    $('#loadMonthBtn').html('<i class="nav-icon icon-refresh"></i> Loading...').prop('disabled', true);

    // AJAX call to load month
    $.ajax({
      url: '{{ route("attendances.load_month") }}',
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        user_id: userId,
        attendance_month: attendanceMonth
      },
      success: function(response) {
        attendanceData = response.attendance_json;
        isLocked = response.attendance_locked;

        // Show summary section
        $('#summarySection').show();

        // Show bulk actions section
        $('#bulkActionsSection').show();

        // Show navigation section
        $('#navigationSection').show();

        // Show lock section if locked
        if (isLocked) {
          $('#lockSection').show();
          $('#bulkActionsSection').hide();
        }

        // Generate table
        generateTable(attendanceMonth);

        // Show editor section
        $('#editorSection').show();

        // Show save section
        $('#saveSection').show();

        // Calculate initial summary
        calculateSummary();

        // Reset button
        $('#loadMonthBtn').html('<i class="nav-icon icon-refresh"></i> Load Month').prop('disabled', false);
      },
      error: function(xhr) {
        alert('Error loading month: ' + xhr.responseJSON.error);
        $('#loadMonthBtn').html('<i class="nav-icon icon-refresh"></i> Load Month').prop('disabled', false);
      }
    });
  });

  // Generate table rows
  function generateTable(month) {
    const date = new Date(month + '-01');
    const year = date.getFullYear();
    const monthIndex = date.getMonth();
    const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
    const tbody = $('#attendanceTableBody');
    tbody.empty();

    for (let day = 1; day <= daysInMonth; day++) {
      const dayKey = String(day).padStart(2, '0');
      const currentDate = new Date(year, monthIndex, day);
      const dayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' });
      const dayOfWeek = currentDate.getDay(); // 0 = Sunday, 6 = Saturday
      
      // Auto-detect weekends (Saturday = 6, Sunday = 0)
      let dayData = attendanceData[dayKey] || { status: '', check_in: '', check_out: '', remarks: '' };
      
      // If no existing data and it's a weekend, auto-mark as Weekly Off
      if (!attendanceData[dayKey] && (dayOfWeek === 0 || dayOfWeek === 6)) {
        dayData.status = 'Weekly Off';
        attendanceData[dayKey] = dayData;
      }

      const row = `
        <tr data-day="${dayKey}">
          <td><strong>${dayKey}</strong></td>
          <td>${dayName}</td>
          <td>
            <input type="time" 
                   class="form-control check-in" 
                   name="attendance_json[${dayKey}][check_in]" 
                   value="${dayData.check_in}" 
                   ${isLocked ? 'disabled' : ''}>
          </td>
          <td>
            <input type="time" 
                   class="form-control check-out" 
                   name="attendance_json[${dayKey}][check_out]" 
                   value="${dayData.check_out}" 
                   ${isLocked ? 'disabled' : ''}>
          </td>
          <td>
            <select class="form-control status" 
                    name="attendance_json[${dayKey}][status]" 
                    ${isLocked ? 'disabled' : ''}>
              <option value="">-</option>
              @foreach(config('myhelpers.attendance_status') as $key => $value)
                <option value="{{ $key }}" ${dayData.status === '{{ $key }}' ? 'selected' : ''}>{{ $value }}</option>
              @endforeach
            </select>
          </td>
          <td>
            <input type="text" 
                   class="form-control remarks" 
                   name="attendance_json[${dayKey}][remarks]" 
                   value="${dayData.remarks}" 
                   ${isLocked ? 'disabled' : ''}>
          </td>
        </tr>
      `;
      tbody.append(row);
    }

    // Add event listeners for live summary
    if (!isLocked) {
      $('.status').on('change', function() {
        validateCheckInOut($(this));
        calculateSummary();
      });
      $('.check-in, .check-out').on('change', function() {
        calculateSummary();
      });
    }
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

  // Navigation: Previous month
  $('#prevMonthBtn').click(function() {
    const currentMonth = $('#attendance_month').val();
    if (currentMonth) {
      const date = new Date(currentMonth + '-01');
      date.setMonth(date.getMonth() - 1);
      const prevMonth = date.toISOString().slice(0, 7);
      $('#attendance_month').val(prevMonth);
      $('#loadMonthBtn').click();
    }
  });

  // Navigation: Next month
  $('#nextMonthBtn').click(function() {
    const currentMonth = $('#attendance_month').val();
    if (currentMonth) {
      const date = new Date(currentMonth + '-01');
      date.setMonth(date.getMonth() + 1);
      const nextMonth = date.toISOString().slice(0, 7);
      $('#attendance_month').val(nextMonth);
      $('#loadMonthBtn').click();
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

    const attendanceMonth = $('#attendance_month').val();
    const date = new Date(attendanceMonth + '-01');
    const year = date.getFullYear();
    const monthIndex = date.getMonth();
    const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
    
    // Calculate total days
    $('#summary_total_days').val(daysInMonth);

    $('.status').each(function() {
      const status = $(this).val();
      if (summary.hasOwnProperty(status)) {
        summary[status]++;
      }
    });

    // Calculate working days (Total - Holiday - Weekly Off)
    const workingDays = daysInMonth - summary.Holiday - summary['Weekly Off'];
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
    e.preventDefault();

    if (isLocked) {
      alert('Cannot save locked attendance');
      return false;
    }

    // Build attendance JSON from form
    const formData = $(this).serializeArray();
    attendanceData = {};

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

    // Submit form
    this.submit();
  });
});
</script>
@endsection
