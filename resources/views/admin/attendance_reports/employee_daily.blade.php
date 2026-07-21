@extends('layouts.admin')

@section('title', 'Employee Daily Attendance Report')
@section('content')
<h3 class="page-header">Employee Daily Attendance Report {{link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <form method="GET" action="{{ route('attendance_reports.employee_daily') }}">
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
                <label>Attendance Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="attendance_date" value="{{ request('attendance_date') }}" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>&nbsp;</label>
                <div>
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-file-text"></i> Generate Report
                  </button>
                  @if(isset($employee) && isset($attendanceDate))
                  <a href="{{ route('attendance_reports.employee_daily_print', ['employee_id' => $employee->id, 'attendance_date' => $attendanceDate]) }}" target="_blank" class="btn btn-default">
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

@if(isset($employee) && isset($attendanceDate))
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
                <td><strong>Attendance Date:</strong></td>
                <td>{{ $attendanceDate }}</td>
              </tr>
              <tr>
                <td><strong>Day Name:</strong></td>
                <td>{{ \Carbon\Carbon::parse($attendanceDate)->format('l') }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <h4>Attendance Details</h4>
            @if($dayData)
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
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
                  <tr>
                    <td class="text-center">
                      @php
                        $status = $dayData['status'] ?? '';
                      @endphp
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
                </tbody>
              </table>
            </div>
            @else
            <div class="alert alert-warning">
              <strong>No Attendance Found</strong> for the selected date.
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
      </div>
    </div>
  </div>
</div>
@endif
@endsection
