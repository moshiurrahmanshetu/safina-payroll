@extends('layouts.admin')

@section('title', 'Attendance Reports')
@section('content')
<h3 class="page-header">Attendance Reports</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Select Report Type</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-body text-center">
                <h4>Employee Daily Attendance</h4>
                <p>One employee, one day</p>
                <a href="{{ route('attendance_reports.employee_daily') }}" class="btn btn-primary btn-block">
                  <i class="fa fa-user"></i> Generate Report
                </a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-body text-center">
                <h4>Employee Monthly Attendance</h4>
                <p>Complete monthly attendance</p>
                <a href="{{ route('attendance_reports.employee_monthly') }}" class="btn btn-primary btn-block">
                  <i class="fa fa-calendar"></i> Generate Report
                </a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-body text-center">
                <h4>Daily Attendance Register</h4>
                <p>All employees for one day</p>
                <a href="{{ route('attendance_reports.daily_register') }}" class="btn btn-primary btn-block">
                  <i class="fa fa-users"></i> Generate Report
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-body text-center">
                <h4>Monthly Attendance Register</h4>
                <p>All employees for one month</p>
                <a href="{{ route('attendance_reports.monthly_register') }}" class="btn btn-primary btn-block">
                  <i class="fa fa-calendar-check-o"></i> Generate Report
                </a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-body text-center">
                <h4>Late Attendance Report</h4>
                <p>Late employees only</p>
                <a href="{{ route('attendance_reports.late_report') }}" class="btn btn-primary btn-block">
                  <i class="fa fa-clock-o"></i> Generate Report
                </a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-body text-center">
                <h4>Department Attendance Report</h4>
                <p>All employees under department</p>
                <a href="{{ route('attendance_reports.department_report') }}" class="btn btn-primary btn-block">
                  <i class="fa fa-building"></i> Generate Report
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-body text-center">
                <h4>Shift Attendance Report</h4>
                <p>Employees assigned to shift</p>
                <a href="{{ route('attendance_reports.shift_report') }}" class="btn btn-primary btn-block">
                  <i class="fa fa-clock"></i> Generate Report
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
