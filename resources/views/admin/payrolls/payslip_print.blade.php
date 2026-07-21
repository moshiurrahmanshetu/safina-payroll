<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->user ? $payroll->user->name : 'Employee' }}</title>
    <style>
        @media print {
            @page {
                size: A4 portrait;
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
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0 0 5px 0;
            color: #333;
        }
        .header p {
            margin: 3px 0;
            color: #666;
        }
        .payslip-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0 15px 0;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 10px; }
        .mt-3 { margin-top: 15px; }
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
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-box {
            display: inline-block;
            width: 30%;
            vertical-align: top;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $siteInfo->company_name ?? 'Safina Park & Resort' }}</h1>
            @if($siteInfo->address)
            <p>{{ $siteInfo->address }}</p>
            @endif
            @if($siteInfo->phone)
            <p>Phone: {{ $siteInfo->phone }}</p>
            @endif
            @if($siteInfo->email)
            <p>Email: {{ $siteInfo->email }}</p>
            @endif
        </div>

        <div class="payslip-title">Payslip</div>

        <!-- Employee Information -->
        <div class="section-title">Employee Information</div>
        <table>
            <tr>
                <th style="width: 25%;">Employee Name</th>
                <td>{{ $payroll->user ? $payroll->user->name : 'N/A' }}</td>
                <th style="width: 25%;">Employee ID</th>
                <td>{{ $payroll->user ? $payroll->user->employee_id : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $payroll->user && $payroll->user->department ? $payroll->user->department->name : 'N/A' }}</td>
                <th>Designation</th>
                <td>{{ $payroll->user && $payroll->user->designation ? $payroll->user->designation->name : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Payroll Month</th>
                <td>{{ $payroll->payroll_month }}</td>
                <th>Approval Status</th>
                <td>{{ ucfirst($payroll->approval_status) }}</td>
            </tr>
        </table>

        <!-- Attendance Summary -->
        <div class="section-title">Attendance Summary</div>
        <table>
            <tr>
                <th>Present</th>
                <th>Late</th>
                <th>Half Day</th>
                <th>Absent</th>
                <th>Leave</th>
                <th>Holiday</th>
                <th>Weekly Off</th>
            </tr>
            <tr>
                <td class="text-center">{{ $attendanceSummary['Present'] }}</td>
                <td class="text-center">{{ $attendanceSummary['Late'] }}</td>
                <td class="text-center">{{ $attendanceSummary['Half Day'] }}</td>
                <td class="text-center">{{ $attendanceSummary['Absent'] }}</td>
                <td class="text-center">{{ $attendanceSummary['Leave'] }}</td>
                <td class="text-center">{{ $attendanceSummary['Holiday'] }}</td>
                <td class="text-center">{{ $attendanceSummary['Weekly Off'] }}</td>
            </tr>
        </table>

        <!-- Earnings -->
        <div class="section-title">Earnings</div>
        <table>
            <tr>
                <th style="width: 70%;">Component</th>
                <th class="text-right" style="width: 30%;">Amount</th>
            </tr>
            <tr>
                <td>Basic Salary</td>
                <td class="text-right">{{ $salary ? number_format($salary->basic_salary, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>House Rent</td>
                <td class="text-right">{{ $salary ? number_format($salary->house_rent, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Medical</td>
                <td class="text-right">{{ $salary ? number_format($salary->medical, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Transport</td>
                <td class="text-right">{{ $salary ? number_format($salary->transport, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Food</td>
                <td class="text-right">{{ $salary ? number_format($salary->food, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Mobile</td>
                <td class="text-right">{{ $salary ? number_format($salary->mobile, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Other Allowance</td>
                <td class="text-right">{{ $salary ? number_format($salary->other_allowance, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Festival Bonus</td>
                <td class="text-right">{{ $salary ? number_format($salary->festival_bonus, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Bonus</td>
                <td class="text-right">{{ number_format($payroll->bonus, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="font-bold">Total Earnings</td>
                <td class="text-right font-bold">{{ number_format($payroll->generated_salary + $payroll->bonus, 2) }}</td>
            </tr>
        </table>

        <!-- Deductions -->
        <div class="section-title">Deductions</div>
        <table>
            <tr>
                <th style="width: 70%;">Component</th>
                <th class="text-right" style="width: 30%;">Amount</th>
            </tr>
            <tr>
                <td>Late Deduction ({{ $attendanceSummary['Late'] }} × {{ $salary ? number_format($salary->late_fine, 2) : '0.00' }})</td>
                <td class="text-right">{{ number_format($lateDeduction, 2) }}</td>
            </tr>
            <tr>
                <td>Absent Deduction ({{ $effectiveAbsent }} × {{ $salary ? number_format($salary->absent_deduction, 2) : '0.00' }})</td>
                <td class="text-right">{{ number_format($absentDeduction, 2) }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td class="text-right">{{ $salary ? number_format($salary->tax, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>PF (Provident Fund)</td>
                <td class="text-right">{{ $salary ? number_format($salary->pf, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Other Deduction</td>
                <td class="text-right">{{ $salary ? number_format($salary->other_deduction, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Advance Salary</td>
                <td class="text-right">{{ $salary ? number_format($salary->advance_salary, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td>Additional Deduction</td>
                <td class="text-right">{{ number_format($payroll->deduction, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="font-bold">Total Deductions</td>
                <td class="text-right font-bold">{{ number_format($lateDeduction + $absentDeduction + ($salary ? $salary->tax + $salary->pf + $salary->other_deduction + $salary->advance_salary : 0) + $payroll->deduction, 2) }}</td>
            </tr>
        </table>

        <!-- Net Salary -->
        <table style="margin-top: 20px;">
            <tr style="background-color: #3c8dbc; color: rgb(1, 7, 53);">
                <th style="width: 70%; font-size: 14px;">NET PAYABLE SALARY</th>
                <th class="text-right" style="width: 30%; font-size: 16px;">{{ number_format($payroll->net_salary, 2) }}</th>
            </tr>
        </table>

        <!-- Approval Information -->
        <div class="section-title">Approval Information</div>
        <table>
            <tr>
                <th style="width: 25%;">Submitted At</th>
                <td>{{ $payroll->submitted_at ? $payroll->submitted_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                <th style="width: 25%;">Approved At</th>
                <td>{{ $payroll->approved_at ? $payroll->approved_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Approved By</th>
                <td>{{ $payroll->approver ? $payroll->approver->name : 'N/A' }}</td>
                <th>Created By</th>
                <td>{{ $payroll->creator ? $payroll->creator->name : 'N/A' }}</td>
            </tr>
            @if($payroll->approval_remark)
            <tr>
                <th colspan="4">Approval Remark</th>
            </tr>
            <tr>
                <td colspan="4">{{ $payroll->approval_remark }}</td>
            </tr>
            @endif
        </table>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="signature-box">
                        <div class="signature-line">Prepared By</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="signature-box">
                        <div class="signature-line">Checked By</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="signature-box">
                        <div class="signature-line">Approved By</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 30px; text-align: center; font-size: 9px; color: #999;">
            <p>This is a computer-generated payslip. No signature required.</p>
            <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
