<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Attendance Register - Print</title>
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

        <div class="row">
            <div class="col-md-12">
                @if(count($attendanceData) > 0)
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">SL</th>
                            <th class="text-center">Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Assigned Shift</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Check In</th>
                            <th class="text-center">Check Out</th>
                            <th class="text-center">Late Min</th>
                            <th class="text-center">Worked Min</th>
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
                        <tr style="page-break-inside: avoid;">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $employee->employee_id ?? $employee->id }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->department->name ?? 'N/A' }}</td>
                            <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                            <td>{{ $employee->shift->name ?? 'N/A' }}</td>
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
                        <td style="width: 25%;">
                            <strong>Prepared By:</strong>
                            <br><br><br>
                            __________________
                            <br><br>
                        </td>
                        <td style="width: 25%;">
                            <strong>Checked By:</strong>
                            <br><br><br>
                            __________________
                            <br><br>
                        </td>
                        <td style="width: 25%;">
                            <strong>Approved By:</strong>
                            <br><br><br>
                            __________________
                            <br><br>
                        </td>
                        <td style="width: 25%;">
                            <strong>Received By:</strong>
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
