@extends('layouts.admin')
@section('title', 'Attendance List')
@section('content')
<h3 class="page-header">Attendance List @if($attendanceMonths) ({{count($attendanceMonths)}}) @endif {{link_to_route('attendances.create','Add Attendance Month',[],array('class'=>'btn btn-success pull-right'))}}</h3>

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
          <label>Month:</label>
          <input type="month" name="attendance_month" class="form-control" value="{{ request('attendance_month') }}">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Locked:</label>
          <select name="attendance_locked" class="form-control">
            <option value="">All</option>
            <option value="0" {{ request('attendance_locked') === '0' ? 'selected' : '' }}>Unlocked</option>
            <option value="1" {{ request('attendance_locked') === '1' ? 'selected' : '' }}>Locked</option>
          </select>
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
            <th>Month</th>
            <th>Present</th>
            <th>Late</th>
            <th>Half Day</th>
            <th>Absent</th>
            <th>Leave</th>
            <th>Holiday</th>
            <th>Weekly Off</th>
            <th>Locked</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($attendanceMonths as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->user ? $data->user->name : 'N/A'}}</strong></td>
          <td>{{$data->attendance_month}}</td>
          <td>{{$data->summary_present}}</td>
          <td>{{$data->summary_late}}</td>
          <td>{{$data->summary_halfday}}</td>
          <td>{{$data->summary_absent}}</td>
          <td>{{$data->summary_leave}}</td>
          <td>{{$data->summary_holiday}}</td>
          <td>{{$data->summary_weekly_off}}</td>
          <td>
            @if($data->attendance_locked)
              <span class="badge badge-danger">Locked</span>
            @else
              <span class="badge badge-success">Unlocked</span>
            @endif
          </td>
          <td>
           {!! HTML::decode(link_to_route('attendances.show', '<i class="nav-icon icon-eye"></i>', array($data->id))) !!}
           @if(!$data->attendance_locked)
             {!! HTML::decode(link_to_route('attendances.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id))) !!}
             {{ Form::open(array('route' => array('attendances.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
             <button type="submit" class="btn btn-danger delete-form"><i class="nav-icon icon-trash"></i></button>
             {{ Form::close() }}
           @endif
           @if($data->attendance_locked)
             {!! HTML::decode(link_to_route('attendances.unlock', '<i class="nav-icon icon-lock-open"></i>', array($data->id))) !!}
           @else
             {!! HTML::decode(link_to_route('attendances.lock', '<i class="nav-icon icon-lock"></i>', array($data->id))) !!}
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
<script>
$(document).ready(function() {
  $('.delete-form').click(function(e) {
    e.preventDefault();
    if(confirm('Are you sure you want to delete this attendance month?')) {
      $(this).closest('form').submit();
    }
  });
});
</script>
@endsection
