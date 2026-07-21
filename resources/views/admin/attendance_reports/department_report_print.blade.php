<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Attendance Report - Print</title>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-4 { margin-bottom: 15px; }
        .mt-4 { margin-top: 15px; }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -5px;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 5px;
        }
        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 5px;
        }
        h2 {
            font-size: 16px;
            margin: 10px 0 5px 0;
            text-align: center;
        }
        h3 {
            font-size: 13px;
            margin: 5px 0 10px 0;
            text-align: center;
        }
        h4 {
            font-size: 11px;
            margin: 10px 0 5px 0;
            text-decoration: underline;
        }
        p {
            margin: 3px 0;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <h2>{{ $companyName }}</h2>
            @if($companyAddress)
            <p>{{ $companyAddress }}</p>
            @endif
            <h3>{{ $reportTitle }}</h3>
            @php $selectedDept = $departments->firstWhere('id', $departmentId); @endphp
            <p><strong>Department:</strong> {{ $selectedDept ? $selectedDept->name : 'N/A' }}</p>
            <p><strong>Attendance Month:</strong> {{ $attendanceMonth }}</p>
        </div>

        @if(isset($summary))
        <div class="row mb-4">
            <div class="col-md-12">
                <table style="font-size: 8px;">
                    <tr>
                        <td class="text-center" style="width: 25%;"><strong>Total Employees:</strong> {{ $summary['totalEmployees'] }}</td>
                        <td class="text-center" style="width: 25%; color: green;"><strong>Total Present:</strong> {{ $summary['totalPresent'] }}</td>
                        <td class="text-center" style="width: 25%; color: orange;"><strong>Total Late:</strong> {{ $summary['totalLate'] }}</td>
                        <td class="text-center" style="width: 25%; color: red;"><strong>Total Absent:</strong> {{ $summary['totalAbsent'] }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12">
                <table style="font-size: 8px;">
                    <tr>
                        <td class="text-center" style="width: 50%; color: #3c8dbc;"><strong>Average Attendance %:</strong> {{ $summary['averageAttendancePercentage'] }}%</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                @if(count($attendanceData) > 0)
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">SL</th>
                            <th class="text-center">Employee ID</th>
                            <th>Employee Name</th>
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
                        <tr style="page-break-inside: avoid;">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $employee->employee_id ?? $employee->id }}</td>
                            <td>{{ $employee->name }}</td>
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
                @else
                <div style="text-align: center; padding: 20px; border: 1px solid #333;">
                    <strong>No Attendance Found</strong> for the selected criteria.
                </div>
                @endif
            </div>
        </div>

        <div class="row mt-4" style="page-break-inside: avoid;">
            <div class="col-md-12">
                <table>
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
                <table>
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

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
