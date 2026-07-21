@extends('layouts.admin')

@section('title', 'Attendance Dashboard')
@section('content')

<style>

/* =====================================================
   Attendance Dashboard Premium UI
=====================================================*/

.content-wrapper{
    background:#f4f6f9;
}

.page-header{
    font-size:28px;
    font-weight:700;
    margin-bottom:25px;
    color:#2c3e50;
    border-left:5px solid #3c8dbc;
    padding-left:15px;
}

/*==========================
        Small Box
===========================*/

.small-box{
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    transition:.3s;
    border:none;
    margin-bottom: 20px;
}

.small-box:hover{
    transform:translateY(-6px);
    box-shadow:0 18px 40px rgba(0,0,0,.18);
}

.small-box .inner{
    padding:16px;
}

.small-box h3{
    font-size:30px;
    font-weight:700;
    margin-bottom:8px;
}

.small-box p{
    font-size:15px;
    font-weight:600;
}

.small-box .icon{
    top:18px;
    right:18px;
}

.small-box .icon i{
    font-size:30px;
    opacity:.18;
}

.small-box-footer{
    background:rgba(255,255,255,.15)!important;
    color:#0970af!important;
    font-weight:600;
    padding:10px;
}

/*==========================
            BOX
===========================*/

.box{
    border:none;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.box-header{
    background:#fff;
    border-bottom:1px solid #eee;
    padding:18px 22px;
}

.box-title{
    font-size:18px;
    font-weight:700;
    color:#2c3e50;
}

.box-body{
    padding:22px;
}

/*==========================
      Monthly Summary
===========================*/

.box-default .box-body h3{
    font-size:34px;
    font-weight:700;
    margin-bottom:8px;
}

.box-default .box-body small{
    font-size:14px;
    color:#777;
    font-weight:600;
}

/*==========================
          Table
===========================*/

.table{
    margin-bottom:0;
}

.table thead th{
    background:#3c8dbc;
    color:#fff;
    border:none;
    font-size:13px;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.table tbody td{
    vertical-align:middle;
    font-size:13px;
}

.table-striped tbody tr:nth-of-type(odd){
    background:#fafafa;
}

.table tbody tr:hover{
    background:#eef7ff;
}

/*==========================
          Badge
===========================*/

.badge{
    padding:6px 10px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
}

/*==========================
      Quick Action Button
===========================*/

.btn-block{
    border-radius:10px;
    font-weight:600;
    padding:11px;
    transition:.25s;
}

.btn-block:hover{
    transform:translateY(-2px);
}

/*==========================
        Chart
===========================*/

canvas{
    width:100%!important;
    height:320px!important;
}

/*==========================
        Responsive
===========================*/

@media(max-width:991px){

    .small-box h3{
        font-size:30px;
    }

    .box-title{
        font-size:16px;
    }

    canvas{
        height:260px!important;
    }

}

@media(max-width:767px){

    .page-header{
        font-size:22px;
    }

    .small-box{
        margin-bottom:20px;
    }

}
.bg-present{
  background: #00ffc6;
}
.bg-default{
  background: #b6e8ff;
}
.bg-maroon{
  background: #ffe4c4;
}

</style>

<h3 class="page-header">Attendance Dashboard</h3>

<!-- Today's Statistics Cards -->
<div class="row">
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-default">
      <div class="inner">
        <h3>{{ $todayStats['totalEmployees'] }}</h3>
        <p>Total Employees</p>
      </div>
      <div class="icon">
        <i class="fa fa-users"></i>
      </div>
      <a href="{{ route('attendance_reports.employee_monthly') }}" class="small-box-footer">
        More info <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-present">
      <div class="inner">
        <h3>{{ $todayStats['present'] }}</h3>
        <p>Present Today</p>
      </div>
      <div class="icon">
        <i class="fa fa-check-circle"></i>
      </div>
      <a href="{{ route('attendance_reports.daily_register') }}" class="small-box-footer">
        More info <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-yellow">
      <div class="inner">
        <h3>{{ $todayStats['late'] }}</h3>
        <p>Late Today</p>
      </div>
      <div class="icon">
        <i class="fa fa-clock-o"></i>
      </div>
      <a href="{{ route('attendance_reports.late_report') }}" class="small-box-footer">
        More info <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-red">
      <div class="inner">
        <h3>{{ $todayStats['absent'] }}</h3>
        <p>Absent Today</p>
      </div>
      <div class="icon">
        <i class="fa fa-times-circle"></i>
      </div>
      <a href="{{ route('attendance_reports.absent_report') }}" class="small-box-footer">
        More info <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $todayStats['halfDay'] }}</h3>
        <p>Half Day Today</p>
      </div>
      <div class="icon">
        <i class="fa fa-adjust"></i>
      </div>
      <a href="#" class="small-box-footer">
        More info <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-teal">
      <div class="inner">
        <h3>{{ $todayStats['leave'] }}</h3>
        <p>Leave Today</p>
      </div>
      <div class="icon">
        <i class="fa fa-plane"></i>
      </div>
      <a href="{{ route('attendance_reports.leave_report') }}" class="small-box-footer">
        More info <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-gray">
      <div class="inner">
        <h3>{{ $todayStats['holiday'] }}</h3>
        <p>Holiday Today</p>
      </div>
      <div class="icon">
        <i class="fa fa-calendar"></i>
      </div>
      <a href="#" class="small-box-footer">
        More info <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-maroon">
      <div class="inner">
        <h3>{{ $todayStats['weeklyOff'] }}</h3>
        <p>Weekly Off Today</p>
      </div>
      <div class="icon">
        <i class="fa fa-calendar-o"></i>
      </div>
      <a href="#" class="small-box-footer">
        More info <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
</div>
<br>
<!-- Monthly Summary Cards -->
<div class="row">
  <div class="col-md-2">
    <div class="box box-default">
      <div class="box-body text-center">
        <h3 style="color: green;">{{ $monthlyStats['totalPresent'] }}</h3>
        <small>Total Present</small>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="box box-default">
      <div class="box-body text-center">
        <h3 style="color: orange;">{{ $monthlyStats['totalLate'] }}</h3>
        <small>Total Late</small>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="box box-default">
      <div class="box-body text-center">
        <h3 style="color: purple;">{{ $monthlyStats['totalHalfDay'] }}</h3>
        <small>Total Half Day</small>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="box box-default">
      <div class="box-body text-center">
        <h3 style="color: red;">{{ $monthlyStats['totalAbsent'] }}</h3>
        <small>Total Absent</small>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="box box-default">
      <div class="box-body text-center">
        <h3 style="color: teal;">{{ $monthlyStats['totalLeave'] }}</h3>
        <small>Total Leave</small>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="box box-default">
      <div class="box-body text-center">
        <h3 style="color: #3c8dbc;">{{ $monthlyStats['attendancePercentage'] }}%</h3>
        <small>Attendance %</small>
      </div>
    </div>
  </div>
</div>
<br>
<!-- Charts Row 1 -->
<div class="row">
  <div class="col-md-8">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Monthly Attendance Trend (Last 12 Months)</h3>
      </div>
      <div class="box-body">
        <canvas id="trendChart" style="height: 300px;"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Attendance Distribution</h3>
      </div>
      <div class="box-body">
        <canvas id="distributionChart" style="height: 300px;"></canvas>
      </div>
    </div>
  </div>
</div>
<br>

<!-- Charts Row 2 -->
<div class="row">
  <div class="col-md-6">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Department Wise Attendance %</h3>
      </div>
      <div class="box-body">
        <canvas id="departmentChart" style="height: 300px;"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Weekly Attendance (Last 7 Days)</h3>
      </div>
      <div class="box-body">
        <canvas id="weeklyChart" style="height: 300px;"></canvas>
      </div>
    </div>
  </div>
</div>
<br>

<!-- Recent Attendance Table -->
<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Recent Attendance (Latest 15)</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Late Minutes</th>
              </tr>
            </thead>
            <tbody>
              @if(count($recentAttendance) > 0)
                @foreach($recentAttendance as $attendance)
                <tr>
                  <td>{{ $attendance['employee']->name }}</td>
                  <td>{{ $attendance['department'] ? $attendance['department']->name : 'N/A' }}</td>
                  <td>{{ $attendance['checkIn'] }}</td>
                  <td>{{ $attendance['checkOut'] }}</td>
                  <td>
                    @if($attendance['status'] == 'Present')
                      <span class="badge bg-green">{{ $attendance['status'] }}</span>
                    @elseif($attendance['status'] == 'Late')
                      <span class="badge bg-yellow">{{ $attendance['status'] }}</span>
                    @elseif($attendance['status'] == 'Absent')
                      <span class="badge bg-red">{{ $attendance['status'] }}</span>
                    @elseif($attendance['status'] == 'Leave')
                      <span class="badge bg-teal">{{ $attendance['status'] }}</span>
                    @else
                      <span class="badge bg-gray">{{ $attendance['status'] }}</span>
                    @endif
                  </td>
                  <td>{{ $attendance['lateMinutes'] }}</td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="6" class="text-center">No recent attendance records found</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<br>

<!-- Employees on Leave Today & Late Today -->
<div class="row">
  <div class="col-md-6">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Employees on Leave Today</h3>
      </div>
      <div class="box-body">
        @if(count($employeesOnLeaveToday) > 0)
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Leave Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($employeesOnLeaveToday as $leave)
              <tr>
                <td>{{ $leave['employee']->name }}</td>
                <td>{{ $leave['department'] ? $leave['department']->name : 'N/A' }}</td>
                <td>{{ $leave['leaveType'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <p class="text-center">No employees on leave today</p>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Employees Late Today</h3>
      </div>
      <div class="box-body">
        @if(count($employeesLateToday) > 0)
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Late Minutes</th>
              </tr>
            </thead>
            <tbody>
              @foreach($employeesLateToday as $late)
              <tr>
                <td>{{ $late['employee']->name }}</td>
                <td>{{ $late['department'] ? $late['department']->name : 'N/A' }}</td>
                <td>{{ $late['lateMinutes'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <p class="text-center">No employees late today</p>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Quick Actions</h3>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="col-md-3">
            <a href="{{ route('attendances.daily') }}" class="btn btn-primary btn-block">
              <i class="fa fa-calendar-day"></i> Daily Attendance
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('attendances.bulk') }}" class="btn btn-primary btn-block">
              <i class="fa fa-calendar"></i> Monthly Attendance
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('attendance_reports.employee_monthly') }}" class="btn btn-info btn-block">
              <i class="fa fa-user"></i> Employee Monthly Report
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('attendance_reports.daily_register') }}" class="btn btn-info btn-block">
              <i class="fa fa-list"></i> Daily Register
            </a>
          </div>
        </div>
        <div class="row" style="margin-top: 10px;">
          <div class="col-md-3">
            <a href="{{ route('attendance_reports.monthly_register') }}" class="btn btn-info btn-block">
              <i class="fa fa-table"></i> Monthly Register
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('attendance_reports.late_report') }}" class="btn btn-warning btn-block">
              <i class="fa fa-clock-o"></i> Late Report
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('attendance_reports.absent_report') }}" class="btn btn-warning btn-block">
              <i class="fa fa-times-circle"></i> Absent Report
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('attendance_reports.leave_report') }}" class="btn btn-warning btn-block">
              <i class="fa fa-plane"></i> Leave Report
            </a>
          </div>
        </div>
        <div class="row" style="margin-top: 10px;">
          <div class="col-md-3">
            <a href="{{ route('attendance_reports.shift_report') }}" class="btn btn-warning btn-block">
              <i class="fa fa-clock"></i> Shift Report
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('attendance_reports.department_report') }}" class="btn btn-warning btn-block">
              <i class="fa fa-building"></i> Department Report
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Monthly Attendance Trend Chart
  const trendCtx = document.getElementById('trendChart').getContext('2d');
  new Chart(trendCtx, {
    type: 'line',
    data: {
      labels: @json(array_column($trendData, 'month')),
      datasets: [
        {
          label: 'Present',
          data: @json(array_column($trendData, 'present')),
          borderColor: 'green',
          backgroundColor: 'rgba(0, 128, 0, 0.1)',
          fill: true,
        },
        {
          label: 'Late',
          data: @json(array_column($trendData, 'late')),
          borderColor: 'orange',
          backgroundColor: 'rgba(255, 165, 0, 0.1)',
          fill: true,
        },
        {
          label: 'Absent',
          data: @json(array_column($trendData, 'absent')),
          borderColor: 'red',
          backgroundColor: 'rgba(255, 0, 0, 0.1)',
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
    },
  });

  // Attendance Distribution Chart
  const distributionCtx = document.getElementById('distributionChart').getContext('2d');
  new Chart(distributionCtx, {
    type: 'doughnut',
    data: {
      labels: ['Present', 'Late', 'Half Day', 'Absent', 'Leave'],
      datasets: [
        {
          data: [{{ $distribution['present'] }}, {{ $distribution['late'] }}, {{ $distribution['halfDay'] }}, {{ $distribution['absent'] }}, {{ $distribution['leave'] }}],
          backgroundColor: ['green', 'orange', 'purple', 'red', 'teal'],
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
    },
  });

  // Department Wise Attendance Chart
  const departmentCtx = document.getElementById('departmentChart').getContext('2d');
  new Chart(departmentCtx, {
    type: 'bar',
    data: {
      labels: @json(array_column($departmentAttendance, 'department')),
      datasets: [
        {
          label: 'Attendance %',
          data: @json(array_column($departmentAttendance, 'attendancePercentage')),
          backgroundColor: '#3c8dbc',
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
        },
      },
    },
  });

  // Weekly Attendance Chart
  const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
  new Chart(weeklyCtx, {
    type: 'bar',
    data: {
      labels: @json(array_column($weeklyData, 'day')),
      datasets: [
        {
          label: 'Present',
          data: @json(array_column($weeklyData, 'present')),
          backgroundColor: 'green',
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
</script>
@endsection
