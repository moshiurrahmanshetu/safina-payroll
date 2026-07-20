@extends('layouts.admin')
@section('title', 'Salary Revisions')
@section('content')
<h3 class="page-header">Salary Revisions @if($salaryRevisions) ({{count($salaryRevisions)}}) @endif {{link_to_route('salary_revisions.create','New Salary Revision',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'salary_revisions.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
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
            <a href="{{ route('salary_revisions.index') }}" class="btn btn-danger">Reset</a>
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
            <th>Revision Date</th>
            <th>Effective From</th>
            <th>Gross Salary</th>
            <th>Net Salary</th>
            <th>Reason</th>
            <th>Current</th>
            <th>Locked</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($salaryRevisions as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->user ? $data->user->name : 'N/A'}}</strong></td>
          <td>{{$data->created_at ? $data->created_at->format('Y-m-d') : 'N/A'}}</td>
          <td>{{$data->effective_from->format('Y-m-d')}}</td>
          <td><strong>{{$data->gross_salary}}</strong></td>
          <td><strong>{{$data->net_salary}}</strong></td>
          <td>{{$data->salary_increment_reason}}</td>
          <td>
            @if($data->is_current)
              <span class="badge badge-success">Current</span>
            @else
              <span class="badge badge-secondary">Old</span>
            @endif
          </td>
          <td>
            @if($data->salary_locked)
              <span class="badge badge-danger">Locked</span>
            @else
              <span class="badge badge-secondary">Unlocked</span>
            @endif
          </td>
          <td>
           <a href="{{ route('salary_revisions.show', $data->id) }}" class="btn btn-info"><i class="nav-icon icon-eye"></i></a>
           @if($data->salary_locked)
             <a href="{{ route('salary_revisions.unlock', $data->id) }}" class="btn btn-warning"><i class="nav-icon icon-lock-open"></i></a>
           @else
             <a href="{{ route('salary_revisions.lock', $data->id) }}" class="btn btn-secondary"><i class="nav-icon icon-lock"></i></a>
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
