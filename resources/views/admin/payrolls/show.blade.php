@extends('layouts.admin')
@section('title', 'Payroll Details')
@section('content')
<h3 class="page-header">Payroll Details {{link_to_route('payrolls.index','Payroll List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

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
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Late</label>
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Half Day</label>
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Absent</label>
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Leave</label>
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Holiday</label>
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Weekly Off</label>
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Late Deduction</label>
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Absent Deduction</label>
              <input type="text" class="form-control" value="0" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Effective Absent</label>
              <input type="text" class="form-control" value="0" readonly>
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
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->basic_salary : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">House Rent</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->house_rent : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Medical</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->medical : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Transport</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->transport : 'N/A' }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Food</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->food : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Mobile</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->mobile : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Other Allowance</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->other_allowance : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Festival Bonus</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->festival_bonus : 'N/A' }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Tax</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->tax : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">PF</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->pf : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Other Deduction</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->other_deduction : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">Advance Salary</label>
              <input type="text" class="form-control" value="{{ $payroll->salaryStructure ? $payroll->salaryStructure->advance_salary : 'N/A' }}" readonly>
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
