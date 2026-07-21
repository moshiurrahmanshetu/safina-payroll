@extends('admin.attendance_reports.print.layout')

@section('title', 'Employee Monthly Attendance Report - Print')

@section('report_title', 'Employee Monthly Attendance Report')
@section('report_subtitle', 'Monthly Attendance Summary')

@section('content')
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
                        <span style="color: red; font-weight: bold;">Locked</span>
                    @else
                        <span style="color: green; font-weight: bold;">Unlocked</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h4>Summary</h4>
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
                    <td class="text-center"><span style="color: green; font-weight: bold;">{{ $attendanceMonth->summary_present ?? 0 }}</span></td>
                    <td class="text-center"><span style="color: orange; font-weight: bold;">{{ $attendanceMonth->summary_late ?? 0 }}</span></td>
                    <td class="text-center"><span style="color: #17a2b8; font-weight: bold;">{{ $attendanceMonth->summary_halfday ?? 0 }}</span></td>
                    <td class="text-center"><span style="color: red; font-weight: bold;">{{ $attendanceMonth->summary_absent ?? 0 }}</span></td>
                    <td class="text-center"><span style="color: #007bff; font-weight: bold;">{{ $attendanceMonth->summary_leave ?? 0 }}</span></td>
                    <td class="text-center"><span style="color: #9b59b6; font-weight: bold;">{{ $attendanceMonth->summary_holiday ?? 0 }}</span></td>
                    <td class="text-center"><span style="color: gray; font-weight: bold;">{{ $attendanceMonth->summary_weekly_off ?? 0 }}</span></td>
                    <td class="text-center"><strong>{{ $totalHolidays ?? 0 }}</strong></td>
                    <td class="text-center"><strong>{{ $totalWeeklyOff ?? 0 }}</strong></td>
                    <td class="text-center"><strong>{{ $expectedWorkingDays ?? 0 }}</strong></td>
                    <td class="text-center"><strong>{{ $attendancePercentage ?? 0 }}%</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4>Attendance Details</h4>
        <table class="table table-bordered">
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
                    $daysInMonth = Carbon\Carbon::parse($attendanceMonth->attendance_month . '-01')->daysInMonth;
                @endphp
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date = Carbon\Carbon::parse($attendanceMonth->attendance_month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT));
                        $dateKey = $date->format('Y-m-d');
                        $dayData = $attendanceJson[$dateKey] ?? [];
                        $status = $dayData['status'] ?? '';
                    @endphp
                    <tr style="page-break-inside: avoid;">
                        <td class="text-center">{{ $date->format('Y-m-d') }}</td>
                        <td class="text-center">{{ $date->format('l') }}</td>
                        <td class="text-center">
                            @if($status)
                                @if($status == 'Present')
                                    <span style="color: green; font-weight: bold;">{{ $status }}</span>
                                @elseif($status == 'Late')
                                    <span style="color: orange; font-weight: bold;">{{ $status }}</span>
                                @elseif($status == 'Half Day')
                                    <span style="color: #17a2b8; font-weight: bold;">{{ $status }}</span>
                                @elseif($status == 'Absent')
                                    <span style="color: red; font-weight: bold;">{{ $status }}</span>
                                @elseif($status == 'Leave')
                                    <span style="color: #007bff; font-weight: bold;">{{ $status }}</span>
                                @elseif($status == 'Holiday')
                                    <span style="color: #9b59b6; font-weight: bold;">{{ $status }}</span>
                                @elseif($status == 'Weekly Off')
                                    <span style="color: gray; font-weight: bold;">{{ $status }}</span>
                                @else
                                    <span style="color: gray;">{{ $status }}</span>
                                @endif
                            @else
                                <span style="color: gray;">-</span>
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

<div class="row mt-4" style="page-break-inside: avoid;">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <td><strong>Generated By:</strong> {{ $generatedBy ?? 'System' }}</td>
                <td><strong>Generated Date:</strong> {{ $generatedDate ?? '-' }}</td>
                <td><strong>Printed Date:</strong> {{ $printedDate ?? '-' }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-4" style="page-break-inside: avoid;">
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

<script>
    window.onload = function() {
        window.print();
    };
</script>
@endsection
