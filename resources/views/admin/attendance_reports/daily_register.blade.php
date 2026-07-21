@extends('layouts.admin')

@section('title', 'Daily Attendance Register')
@section('content')
<h3 class="page-header">Daily Attendance Register {{link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <form method="GET" action="{{ route('attendance_reports.daily_register') }}">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Attendance Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="attendance_date" value="{{ request('attendance_date') }}" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Department (Optional)</label>
                <select class="form-control" name="department_id">
                  <option value="">-- All Departments --</option>
                  @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                      {{ $department->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Shift (Optional)</label>
                <select class="form-control" name="shift_id">
                  <option value="">-- All Shifts --</option>
                  @foreach($shifts as $shift)
                    <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                      {{ $shift->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Status (Optional)</label>
                <select class="form-control" name="status">
                  <option value="">-- All Status --</option>
                  @foreach($statuses as $key => $value)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                      {{ $value }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-file-text"></i> Generate Report
                </button>
                @if(isset($attendanceDate))
                <a href="{{ route('attendance_reports.daily_register_print', ['attendance_date' => $attendanceDate, 'department_id' => request('department_id'), 'shift_id' => request('shift_id'), 'status' => request('status')]) }}" target="_blank" class="btn btn-default">
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

@if(isset($attendanceDate))
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
          <p><strong>Attendance Date:</strong> {{ $attendanceDate }}</p>
          @if(request('department_id'))
          @php $selectedDept = $departments->firstWhere('id', request('department_id')); @endphp
          <p><strong>Department:</strong> {{ $selectedDept ? $selectedDept->name : 'All Departments' }}</p>
          @endif
          @if(request('shift_id'))
          @php $selectedShift = $shifts->firstWhere('id', request('shift_id')); @endphp
          <p><strong>Shift:</strong> {{ $selectedShift ? $selectedShift->name : 'All Shifts' }}</p>
          @endif
        </div>

        @if(isset($summary))
        <div class="row mb-4">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-1">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4>{{ $summary['total'] }}</h4>
                    <small>Total</small>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: green;">{{ $summary['present'] }}</h4>
                    <small>Present</small>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: orange;">{{ $summary['late'] }}</h4>
                    <small>Late</small>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: #f0ad4e;">{{ $summary['halfDay'] }}</h4>
                    <small>Half Day</small>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: red;">{{ $summary['absent'] }}</h4>
                    <small>Absent</small>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: #007bff;">{{ $summary['leave'] }}</h4>
                    <small>Leave</small>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: #9b59b6;">{{ $summary['holiday'] }}</h4>
                    <small>Holiday</small>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: gray;">{{ $summary['weeklyOff'] }}</h4>
                    <small>Weekly Off</small>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: #3c8dbc;">{{ $summary['attendancePercentage'] }}%</h4>
                    <small>Attendance %</small>
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
                    <th class="text-center">Status</th>
                    <th class="text-center">Check In</th>
                    <th class="text-center">Check Out</th>
                    <th class="text-center">Late Minutes</th>
                    <th class="text-center">Worked Minutes</th>
                    <th>System Remark</th>
                    <th>HR Remark</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($attendanceData as $index => $data)
                  @php
                    $employee = $data['employee'];
                    $dayData = $data['dayData'];
                    $status = $dayData['status'] ?? '';
                  @endphp
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $employee->employee_id ?? $employee->id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->department->name ?? 'N/A' }}</td>
                    <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                    <td>{{ $data['assignedShift'] ? $data['assignedShift']->name : 'N/A' }}</td>
                    <td class="text-center">
                      @if($status)
                        @if($status == 'Present')
                          <span class="badge badge-success">{{ $status }}</span>
                        @elseif($status == 'Late')
                          <span class="badge badge-warning">{{ $status }}</span>
                        @elseif($status == 'Half Day')
                          <span class="badge" style="background-color: #f0ad4e;">{{ $status }}</span>
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
