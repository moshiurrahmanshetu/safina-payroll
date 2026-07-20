@extends('layouts.admin')
@section('title', 'Cash Payment Report')
@section('content')
<h3 class="page-header">Cash Payment Report {{link_to_route('salary_disbursements.index','Back',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'salary_disbursements.cash_payment', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label>From Date:</label>
          <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>To Date:</label>
          <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('salary_disbursements.cash_payment') }}" class="btn btn-danger">Reset</a>
          </div>
        </div>
      </div>
    </div>
    {{ Form::close() }}
  </div>
</div>
<br>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Cash Payment Summary</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <strong>Total Cash Payments:</strong> {{ $disbursements->count() }}
          </div>
          <div class="col-md-6">
            <strong>Total Cash Amount:</strong> {{ number_format($totalAmount, 2) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<br>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Employee</th>
            <th>Payroll Month</th>
            <th>Payment Date</th>
            <th>Reference</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($disbursements as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->employee ? $data->employee->name : 'N/A'}}</strong></td>
          <td>{{$data->payroll ? $data->payroll->payroll_month : 'N/A'}}</td>
          <td>{{$data->payment_date->format('Y-m-d')}}</td>
          <td>{{$data->reference_number ?? '-'}}</td>
          <td><strong>{{ number_format($data->amount, 2) }}</strong></td>
          <td>
            @if($data->payment_status == 'Paid')
              <span class="badge badge-success">Paid</span>
            @elseif($data->payment_status == 'Pending')
              <span class="badge badge-warning">Pending</span>
            @elseif($data->payment_status == 'Cancelled')
              <span class="badge badge-danger">Cancelled</span>
            @endif
          </td>
        </tr>
        @php $i=$i+1; @endphp
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
@section('script')

@endsection
