<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $disbursement->employee ? $disbursement->employee->name : 'N/A' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .payslip {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .company-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .employee-info div {
            flex: 1;
        }
        .employee-info strong {
            display: block;
            color: #333;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h3 {
            background-color: #f0f0f0;
            padding: 10px;
            margin: 0 0 15px 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .summary-box {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin: 0 10px;
        }
        .summary-box h4 {
            margin: 0 0 15px 0;
            color: #333;
        }
        .summary-box .amount {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .approval-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
        }
        .qr-placeholder {
            width: 100px;
            height: 100px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .payslip {
                box-shadow: none;
                border: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="payslip">
        <div class="header">
            <h1>PAYSLIP</h1>
            <p>Salary Disbursement Receipt</p>
        </div>

        <div class="company-info">
            <h2>Company Name</h2>
            <p>123 Business Street, City, Country</p>
            <p>Phone: +1 234 567 890 | Email: info@company.com</p>
        </div>

        <div class="employee-info">
            <div>
                <strong>Employee Name</strong>
                {{ $disbursement->employee ? $disbursement->employee->name : 'N/A' }}
            </div>
            <div>
                <strong>Designation</strong>
                {{ $disbursement->employee && $disbursement->employee->designation ? $disbursement->employee->designation->name : 'N/A' }}
            </div>
            <div>
                <strong>Employee ID</strong>
                {{ $disbursement->employee ? $disbursement->employee->employee_id : 'N/A' }}
            </div>
        </div>

        <div class="employee-info">
            <div>
                <strong>Payroll Month</strong>
                {{ $disbursement->payroll ? $disbursement->payroll->payroll_month : 'N/A' }}
            </div>
            <div>
                <strong>Payment Date</strong>
                {{ $disbursement->payment_date->format('Y-m-d') }}
            </div>
            <div>
                <strong>Payslip ID</strong>
                #{{ str_pad($disbursement->id, 6, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <div class="section">
            <h3>Attendance Summary</h3>
            <table>
                <tr>
                    <th>Total Present</th>
                    <th>Total Late</th>
                    <th>Total Half Day</th>
                    <th>Total Absent</th>
                    <th>Total Leave</th>
                </tr>
                <tr>
                    <td>{{ $disbursement->payroll ? $disbursement->payroll->total_present : 0 }}</td>
                    <td>{{ $disbursement->payroll ? $disbursement->payroll->total_late : 0 }}</td>
                    <td>{{ $disbursement->payroll ? $disbursement->payroll->total_halfday : 0 }}</td>
                    <td>{{ $disbursement->payroll ? $disbursement->payroll->total_absent : 0 }}</td>
                    <td>{{ $disbursement->payroll ? $disbursement->payroll->total_leave : 0 }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>Salary Breakdown - Earnings</h3>
            <table>
                <tr>
                    <th>Component</th>
                    <th>Amount</th>
                </tr>
                @if($disbursement->payroll && $disbursement->payroll->salary)
                <tr>
                    <td>Basic Salary</td>
                    <td>{{ number_format($disbursement->payroll->salary->basic_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>House Rent</td>
                    <td>{{ number_format($disbursement->payroll->salary->house_rent, 2) }}</td>
                </tr>
                <tr>
                    <td>Medical</td>
                    <td>{{ number_format($disbursement->payroll->salary->medical, 2) }}</td>
                </tr>
                <tr>
                    <td>Transport</td>
                    <td>{{ number_format($disbursement->payroll->salary->transport, 2) }}</td>
                </tr>
                <tr>
                    <td>Food</td>
                    <td>{{ number_format($disbursement->payroll->salary->food, 2) }}</td>
                </tr>
                <tr>
                    <td>Mobile</td>
                    <td>{{ number_format($disbursement->payroll->salary->mobile, 2) }}</td>
                </tr>
                <tr>
                    <td>Other Allowance</td>
                    <td>{{ number_format($disbursement->payroll->salary->other_allowance, 2) }}</td>
                </tr>
                <tr>
                    <td>Festival Bonus</td>
                    <td>{{ number_format($disbursement->payroll->salary->festival_bonus, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Gross Salary</strong></td>
                    <td><strong>{{ number_format($disbursement->payroll->salary->gross_salary, 2) }}</strong></td>
                </tr>
                @else
                <tr>
                    <td colspan="2">No salary information available</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="section">
            <h3>Salary Breakdown - Deductions</h3>
            <table>
                <tr>
                    <th>Component</th>
                    <th>Amount</th>
                </tr>
                @if($disbursement->payroll && $disbursement->payroll->salary)
                <tr>
                    <td>Late Fine</td>
                    <td>{{ number_format($disbursement->payroll->salary->late_fine, 2) }}</td>
                </tr>
                <tr>
                    <td>Absent Deduction</td>
                    <td>{{ number_format($disbursement->payroll->salary->absent_deduction, 2) }}</td>
                </tr>
                <tr>
                    <td>Advance Salary</td>
                    <td>{{ number_format($disbursement->payroll->salary->advance_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td>{{ number_format($disbursement->payroll->salary->tax, 2) }}</td>
                </tr>
                <tr>
                    <td>PF</td>
                    <td>{{ number_format($disbursement->payroll->salary->pf, 2) }}</td>
                </tr>
                <tr>
                    <td>Other Deduction</td>
                    <td>{{ number_format($disbursement->payroll->salary->other_deduction, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Deductions</strong></td>
                    <td><strong>{{ number_format($disbursement->payroll->salary->gross_salary - $disbursement->payroll->salary->net_salary, 2) }}</strong></td>
                </tr>
                @else
                <tr>
                    <td colspan="2">No salary information available</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="summary">
            <div class="summary-box">
                <h4>Net Salary</h4>
                <div class="amount">{{ number_format($disbursement->amount, 2) }}</div>
            </div>
            <div class="summary-box">
                <h4>Payment Method</h4>
                <div class="amount">{{ $disbursement->payment_method }}</div>
            </div>
            <div class="summary-box">
                <h4>Reference Number</h4>
                <div class="amount">{{ $disbursement->reference_number ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="approval-info">
            <strong>Approval Information</strong><br>
            Approved By: {{ $disbursement->payroll && $disbursement->payroll->approver ? $disbursement->payroll->approver->name : 'N/A' }}<br>
            Approved Date: {{ $disbursement->payroll && $disbursement->payroll->approved_at ? $disbursement->payroll->approved_at->format('Y-m-d H:i:s') : 'N/A' }}
        </div>

        <div class="qr-placeholder">
            QR Code
        </div>

        <div class="footer">
            <p>This is a computer-generated payslip. No signature required.</p>
            <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
            <p>For any queries, please contact HR Department.</p>
        </div>

        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" class="btn btn-primary">Print Payslip</button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
    </div>
</body>
</html>
