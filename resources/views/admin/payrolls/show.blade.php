@extends('layouts.admin')
@section('title', 'Payroll Details')
@section('content')
<h3 class="page-header">Payroll Details 
  <a href="{{ route('payrolls.index') }}" class="btn btn-success pull-right">Payroll List</a>
  <a href="{{ route('payrolls.payslip_print', $payroll->id) }}" class="btn btn-primary pull-right" style="margin-right: 10px;" target="_blank"><i class="fa fa-print"></i> Print Payslip</a>
</h3>

<!-- Summary Cards -->
<div class="row">
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-aqua">
      <div class="inner">
        <h3>{{ number_format($payroll->generated_salary, 2) }}</h3>
        <p>Generated Salary</p>
      </div>
      <div class="icon">
        <i class="fa fa-money"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-green">
      <div class="inner">
        <h3>{{ number_format($payroll->net_salary, 2) }}</h3>
        <p>Net Salary</p>
      </div>
      <div class="icon">
        <i class="fa fa-check-circle"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-yellow">
      <div class="small-box-footer">
        <span class="badge bg-{{ $payroll->approval_status == 'approved' ? 'green' : ($payroll->approval_status == 'submitted' ? 'blue' : 'gray') }}">{{ ucfirst($payroll->approval_status) }}</span>
      </div>
      <div class="inner">
        <h3>{{ $attendanceSummary['Present'] }}</h3>
        <p>Present Days</p>
      </div>
      <div class="icon">
        <i class="fa fa-calendar-check"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-red">
      <div class="inner">
        <h3>{{ $attendanceSummary['Absent'] }}</h3>
        <p>Absent Days</p>
      </div>
      <div class="icon">
        <i class="fa fa-calendar-times"></i>
      </div>
    </div>
  </div>
</div>
<br>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Employee Information</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Employee</label>
              <input type="text" class="form-control" value="{{ $payroll->user ? $payroll->user->name : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Payroll Month</label>
              <input type="text" class="form-control" value="{{ $payroll->payroll_month }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Approval Status</label>
              <input type="text" class="form-control" value="{{ config('myhelpers.approval_status.' . $payroll->approval_status) }}" readonly>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Salary Details</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Generated Salary</label>
              <input type="text" class="form-control" value="{{ $payroll->generated_salary }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Bonus</label>
              <input type="text" class="form-control" value="{{ $payroll->bonus }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Deduction</label>
              <input type="text" class="form-control" value="{{ $payroll->deduction }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Net Salary</label>
              <input type="text" class="form-control" value="{{ $payroll->net_salary }}" readonly>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <label class="control-label">Remarks</label>
              <textarea class="form-control" rows="3" readonly>{{ $payroll->remarks }}</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Attendance Summary</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Present</label>
              <input type="text" class="form-control" style="background-color: #d4edda; font-weight: bold;" value="{{ $attendanceSummary['Present'] }}" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Late</label>
              <input type="text" class="form-control" style="background-color: #fff3cd; font-weight: bold;" value="{{ $attendanceSummary['Late'] }}" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Half Day</label>
              <input type="text" class="form-control" style="background-color: #e2e3e5; font-weight: bold;" value="{{ $attendanceSummary['Half Day'] }}" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Absent</label>
              <input type="text" class="form-control" style="background-color: #f8d7da; font-weight: bold;" value="{{ $attendanceSummary['Absent'] }}" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Leave</label>
              <input type="text" class="form-control" style="background-color: #d1ecf1; font-weight: bold;" value="{{ $attendanceSummary['Leave'] }}" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Holiday</label>
              <input type="text" class="form-control" style="background-color: #d6d8d9; font-weight: bold;" value="{{ $attendanceSummary['Holiday'] }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Weekly Off</label>
              <input type="text" class="form-control" style="background-color: #d6d8d9; font-weight: bold;" value="{{ $attendanceSummary['Weekly Off'] }}" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Late Deduction</label>
              <input type="text" class="form-control" style="background-color: #fff3cd; font-weight: bold;" value="{{ number_format($lateDeduction, 2) }}" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Absent Deduction</label>
              <input type="text" class="form-control" style="background-color: #f8d7da; font-weight: bold;" value="{{ number_format($absentDeduction, 2) }}" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Effective Absent</label>
              <input type="text" class="form-control" style="background-color: #f8d7da; font-weight: bold;" value="{{ $effectiveAbsent }}" readonly>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Salary Structure Components</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Basic Salary</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->basic_salary, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">House Rent</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->house_rent, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Medical</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->medical, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Transport</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->transport, 2) : 'N/A' }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Food</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->food, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Mobile</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->mobile, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Other Allowance</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->other_allowance, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Festival Bonus</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->festival_bonus, 2) : 'N/A' }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Tax</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->tax, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">PF</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->pf, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Other Deduction</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->other_deduction, 2) : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Advance Salary</label>
              <input type="text" class="form-control" value="{{ $salary ? number_format($salary->advance_salary, 2) : 'N/A' }}" readonly>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Approval History</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Submitted At</label>
              <input type="text" class="form-control" value="{{ $payroll->submitted_at ? $payroll->submitted_at->format('Y-m-d H:i:s') : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Approved At</label>
              <input type="text" class="form-control" value="{{ $payroll->approved_at ? $payroll->approved_at->format('Y-m-d H:i:s') : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Returned At</label>
              <input type="text" class="form-control" value="{{ $payroll->returned_at ? $payroll->returned_at->format('Y-m-d H:i:s') : 'N/A' }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Approved By</label>
              <input type="text" class="form-control" value="{{ $payroll->approver ? $payroll->approver->name : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Returned By</label>
              <input type="text" class="form-control" value="{{ $payroll->returner ? $payroll->returner->name : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Created By</label>
              <input type="text" class="form-control" value="{{ $payroll->creator ? $payroll->creator->name : 'N/A' }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12" style="margin-top: 10px;">
          <div class="form-group">
            <label class="control-label">Approval Remark</label>
            <textarea class="form-control" rows="3" readonly>{{ $payroll->approval_remark }}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($payroll->approval_status == 'submitted')
<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <a href="{{ route('payrolls.approve', $payroll->id) }}" class="btn btn-success">Approve</a>
      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#returnModal">Return</button>
    </div>
  </div>
</div>
@endif

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Return Payroll</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{ Form::open(array('route' => array('payrolls.return', $payroll->id), 'method'=>'POST', 'class'=>'form-horizontal')) }}
        <div class="form-group">
          <label class="control-label">Remark *</label>
          {{Form::textarea('approval_remark',null, array('class' => 'form-control', 'rows'=>'3', 'required'=>'required'))}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Return</button>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

@endsection
