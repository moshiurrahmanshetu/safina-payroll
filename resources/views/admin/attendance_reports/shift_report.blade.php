@extends('layouts.admin')

@section('title', 'Shift Attendance Report')
@section('content')
<h3 class="page-header">Shift Attendance Report {{link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <form method="GET" action="{{ route('attendance_reports.shift_report') }}">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Shift <span class="text-danger">*</span></label>
                <select class="form-control" name="shift_id" required>
                  <option value="">-- Select Shift --</option>
                  @foreach($shifts as $shift)
                    <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                      {{ $shift->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Attendance Month <span class="text-danger">*</span></label>
                <input type="month" class="form-control" name="attendance_month" value="{{ request('attendance_month') }}" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-file-text"></i> Generate Report
                </button>
                @if(isset($attendanceMonth))
                <a href="{{ route('attendance_reports.shift_report_print', ['shift_id' => $shiftId, 'attendance_month' => $attendanceMonth]) }}" target="_blank" class="btn btn-default">
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
        </form>
      </div>
    </div>
  </div>
</div>

@if(isset($attendanceMonth))
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Report Output</h4>
      </div>
      <div class="panel-body">
        <div class="text-center mb-4">
          <h2>{{ $companyName }}</h2>
          @if($companyAddress)
          <p>{{ $companyAddress }}</p>
          @endif
          <h3>{{ $reportTitle }}</h3>
          @php $selectedShift = $shifts->firstWhere('id', $shiftId); @endphp
          <p><strong>Shift:</strong> {{ $selectedShift ? $selectedShift->name : 'N/A' }}</p>
          <p><strong>Attendance Month:</strong> {{ $attendanceMonth }}</p>
        </div>

        @if(isset($summary))
        <div class="row mb-4">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-3">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4>{{ $summary['totalEmployees'] }}</h4>
                    <small>Total Employees</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: green;">{{ $summary['totalPresent'] }}</h4>
                    <small>Total Present</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: orange;">{{ $summary['totalLate'] }}</h4>
                    <small>Total Late</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: red;">{{ $summary['totalAbsent'] }}</h4>
                    <small>Total Absent</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: #3c8dbc;">{{ $summary['averageAttendancePercentage'] }}%</h4>
                    <small>Average Attendance %</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        <div class="row">
          <div class="col-md-12">
            @if(count($attendanceData) > 0)
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px;">SL</th>
                    <th class="text-center">Employee ID</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Assigned Shift</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Late</th>
                    <th class="text-center">Half Day</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Leave</th>
                    <th class="text-center">Holiday</th>
                    <th class="text-center">Weekly Off</th>
                    <th class="text-center">Attendance %</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($attendanceData as $index => $data)
                  @php
                    $employee = $data['employee'];
                    $attendancePercentage = $data['attendancePercentage'];
                  @endphp
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $employee->employee_id ?? $employee->id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->department->name ?? 'N/A' }}</td>
                    <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                    <td>{{ $data['assignedShift'] ? $data['assignedShift']->name : 'N/A' }}</td>
                    <td class="text-center">{{ $data['present'] }}</td>
                    <td class="text-center">{{ $data['late'] }}</td>
                    <td class="text-center">{{ $data['halfDay'] }}</td>
                    <td class="text-center">{{ $data['absent'] }}</td>
                    <td class="text-center">{{ $data['leave'] }}</td>
                    <td class="text-center">{{ $data['holiday'] }}</td>
                    <td class="text-center">{{ $data['weeklyOff'] }}</td>
                    <td class="text-center">
                      @if($attendancePercentage >= 95)
                        <span style="color: green; font-weight: bold;">{{ $attendancePercentage }}%</span>
                      @elseif($attendancePercentage >= 85)
                        <span style="color: #007bff; font-weight: bold;">{{ $attendancePercentage }}%</span>
                      @elseif($attendancePercentage >= 70)
                        <span style="color: orange; font-weight: bold;">{{ $attendancePercentage }}%</span>
                      @else
                        <span style="color: red; font-weight: bold;">{{ $attendancePercentage }}%</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @else
            <div class="alert alert-warning">
              <strong>No Attendance Found</strong> for the selected criteria.
            </div>
            @endif
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
