@extends('layouts.admin')
@section('title', 'Salary Disbursements')
@section('content')
<h3 class="page-header">Salary Disbursements @if($disbursements) ({{count($disbursements)}}) @endif {{link_to_route('salary_disbursements.create','Process Payment',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'salary_disbursements.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Employee Name">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="payment_status" class="form-control">
            <option value="">All</option>
            <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
            <option value="Pending" {{ request('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Cancelled" {{ request('payment_status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Method:</label>
          <select name="payment_method" class="form-control">
            <option value="">All</option>
            <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
            <option value="Bank" {{ request('payment_method') == 'Bank' ? 'selected' : '' }}>Bank</option>
            <option value="Mobile Banking" {{ request('payment_method') == 'Mobile Banking' ? 'selected' : '' }}>Mobile Banking</option>
            <option value="Cheque" {{ request('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>From:</label>
          <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>To:</label>
          <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('salary_disbursements.index') }}" class="btn btn-danger">Reset</a>
          </div>
        </div>
      </div>
    </div>
    {{ Form::close() }}
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
            <th>Method</th>
            <th>Reference</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
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
          <td>{{$data->payment_method}}</td>
          <td>{{$data->reference_number ?? '-'}}</td>
          <td><strong>{{$data->amount}}</strong></td>
          <td>
            @if($data->payment_status == 'Paid')
              <span class="badge badge-success">Paid</span>
            @elseif($data->payment_status == 'Pending')
              <span class="badge badge-warning">Pending</span>
            @elseif($data->payment_status == 'Cancelled')
              <span class="badge badge-danger">Cancelled</span>
            @endif
          </td>
          <td>
           <a href="{{ route('salary_disbursements.show', $data->id) }}" class="btn btn-info"><i class="nav-icon icon-eye"></i></a>
           <a href="{{ route('salary_disbursements.payslip', $data->id) }}" class="btn btn-primary" target="_blank"><i class="nav-icon icon-printer"></i></a>
           @if($data->payment_status == 'Paid')
             <a href="{{ route('salary_disbursements.cancel', $data->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this payment?')"><i class="nav-icon icon-close"></i></a>
           @endif
         </td>
       </tr>
       @php $i=$i+1; @endphp
       @endforeach
     </tbody>
   </table>
 </div>

</div>
</div>

@endsection
@section('script')

@endsection
