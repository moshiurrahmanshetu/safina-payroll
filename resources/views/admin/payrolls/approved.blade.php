@extends('layouts.admin')
@section('title', 'Approved Payroll History')
@section('content')
<h3 class="page-header">Approved Payroll History @if($payrolls) ({{count($payrolls)}}) @endif</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'payrolls.approved', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Employee Name">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>Payroll Month:</label>
          <input type="month" name="payroll_month" class="form-control" value="{{ request('payroll_month') }}">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('payrolls.approved') }}" class="btn btn-danger">Reset</a>
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
            <th>Generated Salary</th>
            <th>Net Salary</th>
            <th>Status</th>
            <th>Approved At</th>
            <th>Approved By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($payrolls as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->user ? $data->user->name : 'N/A'}}</strong></td>
          <td>{{$data->payroll_month}}</td>
          <td>{{$data->generated_salary}}</td>
          <td><strong>{{$data->net_salary}}</strong></td>
          <td>
            <strong class="badge badge-{{config('myhelpers.approval_status_color.approved')}}">{{config('myhelpers.approval_status.approved')}}</strong>
          </td>
          <td>{{$data->approved_at ? $data->approved_at->format('Y-m-d H:i:s') : 'N/A'}}</td>
          <td>{{$data->approver ? $data->approver->name : 'N/A'}}</td>
          <td>
           <a href="{{ route('payrolls.show', $data->id) }}" class="btn btn-info"><i class="nav-icon icon-eye"></i> View</a>
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
