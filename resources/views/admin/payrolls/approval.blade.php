@extends('layouts.admin')
@section('title', 'Payroll Approval')
@section('content')
<h3 class="page-header">Payroll Approval @if($payrolls) ({{count($payrolls)}}) @endif</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'payrolls.approval', 'method'=>'GET', 'class'=>'form-horizontal')) }}
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
            <a href="{{ route('payrolls.approval') }}" class="btn btn-danger">Reset</a>
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
            <th>Submitted Time</th>
            <th>Submitted By</th>
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
            <strong class="badge badge-{{config('myhelpers.approval_status_color.submitted')}}">{{config('myhelpers.approval_status.submitted')}}</strong>
          </td>
          <td>{{$data->submitted_at ? $data->submitted_at->format('Y-m-d H:i:s') : 'N/A'}}</td>
          <td>{{$data->creator ? $data->creator->name : 'N/A'}}</td>
          <td>
           <a href="{{ route('payrolls.show', $data->id) }}" class="btn btn-info"><i class="nav-icon icon-eye"></i> View</a>
           <a href="{{ route('payrolls.approve', $data->id) }}" class="btn btn-success"><i class="nav-icon icon-check"></i> Approve</a>
           <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#returnModal{{$data->id}}"><i class="nav-icon icon-reply"></i> Return</button>
         </td>
       </tr>
       @php $i=$i+1; @endphp
       @endforeach
     </tbody>
   </table>
 </div>

</div>
</div>

<!-- Return Modals -->
@foreach ($payrolls as $data)
<div class="modal fade" id="returnModal{{$data->id}}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Return Payroll</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{ Form::open(array('route' => array('payrolls.return', $data->id), 'method'=>'POST', 'class'=>'form-horizontal')) }}
        <div class="form-group">
          <label class="control-label">Remark *</label>
          {{Form::textarea('approval_remark',null, array('class' => 'form-control', 'rows'=>'3', 'required'=>'required'))}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Return</button>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
@endforeach

@endsection
@section('script')

@endsection
