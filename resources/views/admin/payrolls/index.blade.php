@extends('layouts.admin')
@section('title', 'Payroll List')
@section('content')
<h3 class="page-header">Payroll List @if($payrolls) ({{count($payrolls)}}) @endif {{link_to_route('payrolls.create','Generate Payroll',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'payrolls.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
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
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Draft</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Sent To Manager</option>
            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Approved</option>
            <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('payrolls.index') }}" class="btn btn-danger">Reset</a>
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
            <th>Attendance Adj.</th>
            <th>Bonus</th>
            <th>Deduction</th>
            <th>Net Salary</th>
            <th>Approval Status</th>
            <th>Payment Status</th>
            <th>Current Holder</th>
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
          <td>{{$data->attendance_adjustment}}</td>
          <td>{{$data->bonus}}</td>
          <td>{{$data->deduction}}</td>
          <td><strong>{{$data->net_salary}}</strong></td>
          <td>
            @if($data->approval_status == 'pending')
              <strong class="badge badge-{{config('myhelpers.approval_status_color.pending')}}">{{config('myhelpers.approval_status.pending')}}</strong>
            @elseif($data->approval_status == 'submitted')
              <strong class="badge badge-{{config('myhelpers.approval_status_color.submitted')}}">{{config('myhelpers.approval_status.submitted')}}</strong>
            @elseif($data->approval_status == 'returned')
              <strong class="badge badge-{{config('myhelpers.approval_status_color.returned')}}">{{config('myhelpers.approval_status.returned')}}</strong>
            @elseif($data->approval_status == 'approved')
              <strong class="badge badge-{{config('myhelpers.approval_status_color.approved')}}">{{config('myhelpers.approval_status.approved')}}</strong>
            @elseif($data->approval_status == 'Paid')
              <strong class="badge badge-success">Paid</strong>
            @endif
          </td>
          <td>
            @if($data->payment_status == 'Paid')
              <span class="badge badge-success">Paid</span>
            @elseif($data->payment_status == 'Pending')
              <span class="badge badge-warning">Pending</span>
            @elseif($data->payment_status == 'Cancelled')
              <span class="badge badge-danger">Cancelled</span>
            @else
              <span class="badge badge-secondary">{{ $data->payment_status }}</span>
            @endif
          </td>
          <td>
            @if($data->approval_status == 'pending')
              <span>HR/Admin</span>
            @elseif($data->approval_status == 'submitted')
              <span>Manager</span>
            @elseif($data->approval_status == 'returned')
              <span>HR/Admin</span>
            @elseif($data->approval_status == 'approved')
              <span>Accounts/Payroll Archive</span>
            @endif
          </td>
          <td>
           @if($data->approval_status == 'pending')
             {!! HTML::decode(link_to_route('payrolls.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
             {{ Form::open(array('route' => array('payrolls.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
             <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
             {{ Form::close() }}
             <a href="{{ route('payrolls.submit', $data->id) }}" class="btn btn-success"><i class="nav-icon icon-check"></i></a>
           @elseif($data->approval_status == 'returned')
             {!! HTML::decode(link_to_route('payrolls.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
             <a href="{{ route('payrolls.submit', $data->id) }}" class="btn btn-success"><i class="nav-icon icon-check"></i></a>
             @if($data->approval_remark)
               <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#remarkModal{{$data->id}}"><i class="nav-icon icon-info"></i></button>
             @endif
           @elseif($data->approval_status == 'approved' && !$data->isPaid())
             <a href="{{ route('salary_disbursements.create') }}?payroll_id={{ $data->id }}" class="btn btn-primary"><i class="nav-icon icon-wallet"></i> Pay Salary</a>
           @endif
           <a href="{{ route('payrolls.show', $data->id) }}" class="btn btn-info"><i class="nav-icon icon-eye"></i></a>
         </td>
       </tr>
       @php $i=$i+1; @endphp
       @endforeach
     </tbody>
   </table>
 </div>

</div>
</div>

<!-- Remark Modals for Returned Payrolls -->
@foreach ($payrolls as $data)
@if($data->approval_status == 'returned' && $data->approval_remark)
<div class="modal fade" id="remarkModal{{$data->id}}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Return Remark</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="control-label">Remark:</label>
          <textarea class="form-control" rows="3" readonly>{{ $data->approval_remark }}</textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endforeach

@endsection
@section('script')

@endsection
