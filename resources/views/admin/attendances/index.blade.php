@extends('layouts.admin')
@section('title', 'Attendance List')
@section('content')
<h3 class="page-header">Attendance List @if($attendances) ({{count($attendances)}}) @endif {{link_to_route('attendances.create','Add Attendance',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'attendances.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Employee Name">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            @foreach(config('myhelpers.attendance_status') as $key => $value)
              <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Date From:</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Date To:</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('attendances.index') }}" class="btn btn-danger">Reset</a>
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
            <th>Date</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Created By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($attendances as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->user ? $data->user->name : 'N/A'}}</strong></td>
          <td>{{$data->attendance_date}}</td>
          <td>{{$data->check_in ? $data->check_in : '-'}}</td>
          <td>{{$data->check_out ? $data->check_out : '-'}}</td>
          <td>
            @php $statusColor = config('myhelpers.attendance_status_color.' . $data->status, 'default'); @endphp
            <strong class="btn-{{$statusColor}}">{{$data->status}}</strong>
          </td>
          <td>{{$data->remarks ?? '-'}}</td>
          <td>{{$data->creator ? $data->creator->name : 'N/A'}}</td>
          <td>
           {!! HTML::decode(link_to_route('attendances.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('attendances.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
