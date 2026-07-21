@extends('layouts.admin')

@section('title', 'Leave Attendance Report')
@section('content')
<h3 class="page-header">Leave Attendance Report {{link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <form method="GET" action="{{ route('attendance_reports.leave_report') }}">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>From Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>To Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}" required>
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
                <label>Employee (Optional)</label>
                <select class="form-control" name="employee_id">
                  <option value="">-- All Employees --</option>
                  @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                      {{ $employee->name }} ({{ $employee->employee_id ?? $employee->id }})
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
                @if(isset($fromDate))
                <a href="{{ route('attendance_reports.leave_report_print', ['from_date' => $fromDate, 'to_date' => $toDate, 'department_id' => request('department_id'), 'employee_id' => request('employee_id')]) }}" target="_blank" class="btn btn-default">
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

@if(isset($fromDate))
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
          <p><strong>Date Range:</strong> {{ $fromDate }} to {{ $toDate }}</p>
          @if(request('department_id'))
          @php $selectedDept = $departments->firstWhere('id', request('department_id')); @endphp
          <p><strong>Department:</strong> {{ $selectedDept ? $selectedDept->name : 'All Departments' }}</p>
          @endif
          @if(request('employee_id'))
          @php $selectedEmp = $employees->firstWhere('id', request('employee_id')); @endphp
          <p><strong>Employee:</strong> {{ $selectedEmp ? $selectedEmp->name : 'All Employees' }}</p>
          @endif
        </div>

        @if(isset($summary))
        <div class="row mb-4">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-3">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4>{{ $summary['totalLeaveRecords'] }}</h4>
                    <small>Total Leave Records</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: #3c8dbc;">{{ $summary['totalEmployees'] }}</h4>
                    <small>Total Employees</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: green;">{{ $summary['paidLeave'] }}</h4>
                    <small>Paid Leave</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <h4 style="color: #9b59b6;">{{ $summary['unpaidLeave'] }}</h4>
                    <small>Unpaid Leave</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        <div class="row">
          <div class="col-md-12">
            @if(count($leaveData) > 0)
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
                    <th class="text-center">Attendance Date</th>
                    <th class="text-center">Leave Type</th>
                    <th class="text-center">Status</th>
                    <th>System Remark</th>
                    <th>HR Remark</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($leaveData as $index => $data)
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $data['employee']->employee_id ?? $data['employee']->id }}</td>
                    <td>{{ $data['employee']->name }}</td>
                    <td>{{ $data['employee']->department->name ?? 'N/A' }}</td>
                    <td>{{ $data['employee']->designation->name ?? 'N/A' }}</td>
                    <td>{{ $data['assignedShift'] ? $data['assignedShift']->name : 'N/A' }}</td>
                    <td class="text-center">{{ $data['attendanceDate'] }}</td>
                    <td class="text-center">{{ $data['leaveType'] }}</td>
                    <td class="text-center">
                      <span class="badge badge-info">{{ $data['status'] }}</span>
                    </td>
                    <td>{{ $data['systemRemark'] }}</td>
                    <td>{{ $data['hrRemark'] }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @else
            <div class="alert alert-warning">
              <strong>No Leave Records Found</strong> for the selected criteria.
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
