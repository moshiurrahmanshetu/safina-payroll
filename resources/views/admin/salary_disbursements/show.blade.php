@extends('layouts.admin')
@section('title', 'Salary Disbursement Details')
@section('content')
<h3 class="page-header">Salary Disbursement Details {{link_to_route('salary_disbursements.index','Salary Disbursements',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Payment Information</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Employee:</label>
              <p class="form-control-static"><strong>{{ $disbursement->employee ? $disbursement->employee->name : 'N/A' }}</strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Payroll Month:</label>
              <p class="form-control-static">{{ $disbursement->payroll ? $disbursement->payroll->payroll_month : 'N/A' }}</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Payment Date:</label>
              <p class="form-control-static">{{ $disbursement->payment_date->format('Y-m-d') }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Payment Method:</label>
              <p class="form-control-static"><strong>{{ $disbursement->payment_method }}</strong></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Reference Number:</label>
              <p class="form-control-static">{{ $disbursement->reference_number ?? 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Amount:</label>
              <p class="form-control-static"><strong>{{ number_format($disbursement->amount, 2) }}</strong></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Payment Status:</label>
              <p class="form-control-static">
                @if($disbursement->payment_status == 'Paid')
                  <span class="badge badge-success">Paid</span>
                @elseif($disbursement->payment_status == 'Pending')
                  <span class="badge badge-warning">Pending</span>
                @elseif($disbursement->payment_status == 'Cancelled')
                  <span class="badge badge-danger">Cancelled</span>
                @endif
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Remarks:</label>
              <p class="form-control-static">{{ $disbursement->remarks ?? 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Audit Information -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Audit Information</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Created By:</label>
              <p class="form-control-static">{{ $disbursement->creator ? $disbursement->creator->name : 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Created At:</label>
              <p class="form-control-static">{{ $disbursement->created_at ? $disbursement->created_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Updated By:</label>
              <p class="form-control-static">{{ $disbursement->updater ? $disbursement->updater->name : 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Updated At:</label>
              <p class="form-control-static">{{ $disbursement->updated_at ? $disbursement->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <a href="{{ route('salary_disbursements.payslip', $disbursement->id) }}" class="btn btn-primary" target="_blank"><i class="nav-icon icon-printer"></i> Print Payslip</a>
      @if($disbursement->payment_status == 'Paid')
        <a href="{{ route('salary_disbursements.cancel', $disbursement->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this payment?')"><i class="nav-icon icon-close"></i> Cancel Payment</a>
      @endif
      {!! HTML::decode(link_to_route('salary_disbursements.index', '<i class="nav-icon icon-arrow-left"></i> Back', [], array('class' => 'btn btn-default'))) !!}
    </div>
  </div>
</div>

@endsection
@section('script')

@endsection
