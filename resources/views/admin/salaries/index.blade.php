@extends('layouts.admin')
@section('title', 'Salary History')
@section('content')
<h3 class="page-header">Salary History @if($salaries) ({{count($salaries)}}) @endif</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'salaries.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Employee Name">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>Current Only:</label>
          <select name="is_current" class="form-control">
            <option value="">All</option>
            <option value="1" {{ request('is_current') == '1' ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ request('is_current') == '0' ? 'selected' : '' }}>No</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('salaries.index') }}" class="btn btn-danger">Reset</a>
            <a href="{{ route('salaries.create') }}" class="btn btn-success pull-right"><i class="nav-icon icon-plus"></i> Create Salary Revision</a>
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
            <th>Effective From</th>
            <th>Basic Salary</th>
            <th>Total Salary</th>
            <th>Reason</th>
            <th>Current</th>
            <th>Locked</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($salaries as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->user ? $data->user->name : 'N/A'}}</strong></td>
          <td>{{$data->effective_from->format('Y-m-d')}}</td>
          <td>{{$data->basic_salary}}</td>
          <td><strong>{{$data->total_salary}}</strong></td>
          <td>{{$data->salary_increment_reason}}</td>
          <td>
            @if($data->is_current)
              <span class="badge badge-success">Current</span>
            @else
              <span class="badge badge-secondary">Old</span>
            @endif
          </td>
          <td>
            @if($data->is_locked)
              <span class="badge badge-danger">Locked</span>
            @else
              <span class="badge badge-secondary">Unlocked</span>
            @endif
          </td>
          <td>
            @if($data->status)
              <span class="badge badge-success">Active</span>
            @else
              <span class="badge badge-danger">Inactive</span>
            @endif
          </td>
          <td class="custom-td">
           <a href="{{ route('salaries.show', $data->id) }}" class="btn btn-info"><i class="nav-icon icon-eye"></i></a>
           @if(!$data->is_locked)
             {!! HTML::decode(link_to_route('salaries.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
             @if(!$data->is_current)
               {{ Form::open(array('route' => array('salaries.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
               <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
               {{ Form::close() }}
             @endif
           @endif
           @if($data->is_locked)
             <a href="{{ route('salaries.unlock', $data->id) }}" class="btn btn-warning"><i class="nav-icon icon-lock-open"></i></a>
           @else
             <a href="{{ route('salaries.lock', $data->id) }}" class="btn btn-secondary"><i class="nav-icon icon-lock"></i></a>
           @endif
           <a href="{{ route('salaries.timeline', $data->user_id) }}" class="btn btn-primary"><i class="nav-icon icon-clock"></i></a>
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
