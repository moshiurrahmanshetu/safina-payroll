@extends('layouts.admin')
@section('title', 'Process Salary Payment')
@section('content')
<h3 class="page-header">Process Salary Payment {{link_to_route('salary_disbursements.index','Salary Disbursements',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Select Approved Payroll</h4>
      </div>
      <div class="panel-body">
        @if($approvedPayrolls->count() > 0)
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Payroll Month</th>
                <th>Net Salary</th>
                <th>Approved Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($approvedPayrolls as $payroll)
              <tr>
                <td><strong>{{ $payroll->user ? $payroll->user->name : 'N/A' }}</strong></td>
                <td>{{ $payroll->payroll_month }}</td>
                <td><strong>{{ $payroll->generated_salary }}</strong></td>
                <td>{{ $payroll->approved_at ? $payroll->approved_at->format('Y-m-d') : 'N/A' }}</td>
                <td>
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paymentModal" 
                          data-payroll-id="{{ $payroll->id }}" 
                          data-employee="{{ $payroll->user ? $payroll->user->name : 'N/A' }}"
                          data-payroll-month="{{ $payroll->payroll_month }}"
                          data-net-salary="{{ $payroll->generated_salary }}">
                    Pay Salary
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="alert alert-info">
          No approved payrolls pending payment.
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Process Salary Payment</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h4>Payment Details</h4>
              </div>
              <div class="panel-body">
                <div class="form-group">
                  <label>Employee:</label>
                  <p class="form-control-static" id="modalEmployee">-</p>
                </div>
                <div class="form-group">
                  <label>Payroll Month:</label>
                  <p class="form-control-static" id="modalPayrollMonth">-</p>
                </div>
                <div class="form-group">
                  <label>Net Salary:</label>
                  <p class="form-control-static" id="modalNetSalary">-</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        {{ Form::open(array('route' => 'salary_disbursements.process_payment', 'method'=>'POST', 'class'=>'form-horizontal', 'id'=>'paymentForm')) }}
        <input type="hidden" name="payroll_id" id="payroll_id">
        
        <div class="form-group">
          <label class="control-label">Payment Date *</label>
          <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        
        <div class="form-group">
          <label class="control-label">Payment Method *</label>
          <select name="payment_method" class="form-control" required>
            <option value="">Select Method</option>
            <option value="Cash">Cash</option>
            <option value="Bank">Bank</option>
            <option value="Mobile Banking">Mobile Banking</option>
            <option value="Cheque">Cheque</option>
          </select>
        </div>
        
        <div class="form-group">
          <label class="control-label">Reference Number</label>
          <input type="text" name="reference_number" class="form-control" placeholder="Transaction ID / Cheque Number">
        </div>
        
        <div class="form-group">
          <label class="control-label">Remarks</label>
          <textarea name="remarks" class="form-control" rows="3"></textarea>
        </div>
        
        {{ Form::close() }}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmPayment">Confirm Payment</button>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
<script>
$(document).ready(function() {
  // When modal is shown, populate data
  $('#paymentModal').on('show.bs.modal', function(e) {
    const button = $(e.relatedTarget);
    $('#payroll_id').val(button.data('payroll-id'));
    $('#modalEmployee').text(button.data('employee'));
    $('#modalPayrollMonth').text(button.data('payroll-month'));
    $('#modalNetSalary').text(button.data('net-salary'));
  });
  
  // Confirm payment
  $('#confirmPayment').click(function() {
    const formData = $('#paymentForm').serialize();
    
    $.ajax({
      url: '{{ route("salary_disbursements.process_payment") }}',
      type: 'POST',
      data: formData + '&_token={{ csrf_token() }}',
      success: function(response) {
        alert('Payment processed successfully');
        $('#paymentModal').modal('hide');
        location.reload();
      },
      error: function(xhr) {
        alert('Error processing payment: ' + xhr.responseJSON.error);
      }
    });
  });
});
</script>
@endsection
