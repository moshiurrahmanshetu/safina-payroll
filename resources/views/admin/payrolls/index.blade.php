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
            <th>Status</th>
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
            @if($data->status == 0)
              <strong class="btn-secondary">Draft</strong>
            @elseif($data->status == 1)
              <strong class="btn-info">Sent To Manager</strong>
            @elseif($data->status == 2)
              <strong class="btn-success">Approved</strong>
            @elseif($data->status == 3)
              <strong class="btn-danger">Rejected</strong>
            @endif
          </td>
          <td>
           {!! HTML::decode(link_to_route('payrolls.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('payrolls.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
           <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
           {{ Form::close() }}
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
