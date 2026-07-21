@extends('layouts.admin')

@section('title', 'Employee Monthly Attendance Report')
@section('content')
<h3 class="page-header">Employee Monthly Attendance Report {{link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <form method="GET" action="{{ route('attendance_reports.employee_monthly') }}">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Employee <span class="text-danger">*</span></label>
                <select class="form-control" name="employee_id" required>
                  <option value="">-- Select Employee --</option>
                  @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} ({{ $emp->employee_id ?? $emp->id }})
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Attendance Month <span class="text-danger">*</span></label>
                <input type="month" class="form-control" name="attendance_month" value="{{ request('attendance_month') }}" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>&nbsp;</label>
                <div>
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-file-text"></i> Generate Report
                  </button>
                  @if(isset($employee) && isset($attendanceMonth))
                  <a href="{{ route('attendance_reports.employee_monthly_print', ['employee_id' => $employee->id, 'attendance_month' => $attendanceMonth->attendance_month]) }}" target="_blank" class="btn btn-default">
                    <i class="fa fa-print"></i> Print
                  </a>
                  @endif
                  <button type="button" class="btn btn-default" disabled>
                    <i class="fa fa-file-pdf"></i> Export PDF
                  </button>
                  <button type="button" class="btn btn-default" disabled>
                    <i class="fa fa-file-excel"></i> Export Excel
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@if(isset($employee) && isset($attendanceMonth))
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Report Output</h4>
      </div>
      <div class="panel-body">
        <div class="text-center mb-4">
          <h2>{{ $companyName }}</h2>
          <h3>{{ $reportTitle }}</h3>
        </div>

        <div class="row mb-4">
          <div class="col-md-6">
            <table class="table table-bordered">
              <tr>
                <td><strong>Employee Name:</strong></td>
                <td>{{ $employee->name }}</td>
              </tr>
              <tr>
                <td><strong>Employee ID:</strong></td>
                <td>{{ $employee->employee_id ?? $employee->id }}</td>
              </tr>
              <tr>
                <td><strong>Department:</strong></td>
                <td>{{ $employee->department->name ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-bordered">
              <tr>
                <td><strong>Designation:</strong></td>
                <td>{{ $employee->designation->name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td><strong>Assigned Shift:</strong></td>
                <td>{{ $assignedShift && $assignedShift->shift ? $assignedShift->shift->name : 'N/A' }}</td> 
              </tr>
              <tr>
                <td><strong>Attendance Month:</strong></td>
                <td>{{ $attendanceMonth->attendance_month }}</td>
              </tr>
              <tr>
                <td><strong>Locked Status:</strong></td>
                <td>
                  @if($attendanceMonth->attendance_locked)
                    <span class="badge badge-danger">Locked</span>
                  @else
                    <span class="badge badge-success">Unlocked</span>
                  @endif
                </td>
              </tr>
            </table>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-md-12">
            <h4>Summary</h4>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Present</th>
                    <th class="text-center">Late</th>
                    <th class="text-center">Half Day</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Leave</th>
                    <th class="text-center">Holiday</th>
                    <th class="text-center">Weekly Off</th>
                    <th class="text-center">Total Holidays</th>
                    <th class="text-center">Total Weekly Off</th>
                    <th class="text-center">Expected Working Days</th>
                    <th class="text-center">Attendance %</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-center"><span class="badge badge-success">{{ $attendanceMonth->summary_present ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge badge-warning">{{ $attendanceMonth->summary_late ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge badge-info">{{ $attendanceMonth->summary_halfday ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge badge-danger">{{ $attendanceMonth->summary_absent ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge badge-primary">{{ $attendanceMonth->summary_leave ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge" style="background-color: #9b59b6;">{{ $attendanceMonth->summary_holiday ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge badge-default">{{ $attendanceMonth->summary_weekly_off ?? 0 }}</span></td>
                    <td class="text-center"><strong>{{ $totalHolidays ?? 0 }}</strong></td>
                    <td class="text-center"><strong>{{ $totalWeeklyOff ?? 0 }}</strong></td>
                    <td class="text-center"><strong>{{ $expectedWorkingDays ?? 0 }}</strong></td>
                    <td class="text-center"><strong>{{ $attendancePercentage ?? 0 }}%</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <h4>Attendance Details</h4>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th class="text-center">Date</th>
                    <th class="text-center">Day</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Check In</th>
                    <th class="text-center">Check Out</th>
                    <th class="text-center">Late Minutes</th>
                    <th class="text-center">Worked Minutes</th>
                    <th class="text-center">System Remark</th>
                    <th class="text-center">HR Remark</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                     $daysInMonth = \Carbon\Carbon::parse($attendanceMonth->attendance_month . '-01')->daysInMonth;
                  @endphp
                  @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                      $date = \Carbon\Carbon::parse($attendanceMonth->attendance_month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT));
                      $dateKey = $date->format('Y-m-d');
                      $dayData = $attendanceJson[$dateKey] ?? [];
                      $status = $dayData['status'] ?? '';
                    @endphp
                    <tr>
                      <td class="text-center">{{ $date->format('Y-m-d') }}</td>
                      <td class="text-center">{{ $date->format('l') }}</td>
                      <td class="text-center">
                        @if($status)
                          @if($status == 'Present')
                            <span class="badge badge-success">{{ $status }}</span>
                          @elseif($status == 'Late')
                            <span class="badge badge-warning">{{ $status }}</span>
                          @elseif($status == 'Half Day')
                            <span class="badge badge-info">{{ $status }}</span>
                          @elseif($status == 'Absent')
                            <span class="badge badge-danger">{{ $status }}</span>
                          @elseif($status == 'Leave')
                            <span class="badge badge-primary">{{ $status }}</span>
                          @elseif($status == 'Holiday')
                            <span class="badge" style="background-color: #9b59b6;">{{ $status }}</span>
                          @elseif($status == 'Weekly Off')
                            <span class="badge badge-default">{{ $status }}</span>
                          @else
                            <span class="badge badge-default">{{ $status }}</span>
                          @endif
                        @else
                          <span class="badge badge-default">-</span>
                        @endif
                      </td>
                      <td class="text-center">{{ $dayData['check_in'] ?? '-' }}</td>
                      <td class="text-center">{{ $dayData['check_out'] ?? '-' }}</td>
                      <td class="text-center">{{ $dayData['late_minutes'] ?? '-' }}</td>
                      <td class="text-center">{{ $dayData['worked_minutes'] ?? '-' }}</td>
                      <td>{{ $dayData['system_remark'] ?? '-' }}</td>
                      <td>{{ $dayData['remarks'] ?? '-' }}</td>
                    </tr>
                  @endfor
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-12">
            <table class="table table-bordered">
              <tr>
                <td><strong>Generated By:</strong> {{ $generatedBy ?? 'System' }}</td>
                <td><strong>Generated Date:</strong> {{ $generatedDate ?? '-' }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-12">
            <table class="table table-bordered">
              <tr>
                <td style="width: 33%;">
                  <strong>Prepared By:</strong>
                  <br><br><br>
                  __________________
                  <br><br>
                </td>
                <td style="width: 33%;">
                  <strong>Checked By:</strong>
                  <br><br><br>
                  __________________
                  <br><br>
                </td>
                <td style="width: 34%;">
                  <strong>Approved By:</strong>
                  <br><br><br>
                  __________________
                  <br><br>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection
