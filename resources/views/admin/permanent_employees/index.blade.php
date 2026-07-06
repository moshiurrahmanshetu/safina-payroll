@extends('layouts.admin')
@section('title', 'Permanent Employee List')
@section('content')
<h3 class="page-header">Permanent Employee List @if($permanent_employees) ({{count($permanent_employees)}}) @endif {{link_to_route('permanent_employees.create','Add Permanent Employee',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'permanent_employees.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, Employee ID, Mobile">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Department:</label>
          <select name="department_id" class="form-control">
            <option value="">All Departments</option>
            @foreach($departments as $id => $name)
              <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Designation:</label>
          <select name="designation_id" class="form-control">
            <option value="">All Designations</option>
            @foreach($designations as $id => $name)
              <option value="{{ $id }}" {{ request('designation_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="employment_status" class="form-control">
            <option value="">All Status</option>
            <option value="1" {{ request('employment_status') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('employment_status') == '0' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('permanent_employees.index') }}" class="btn btn-danger">Reset</a>
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
            <th>Employee ID</th>
            <th>Photo</th>
            <th>Full Name</th>
            <th>Mobile</th>
            <th>Department</th>
            <th>Designation</th>
            <th>Joining Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($permanent_employees as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->employee_id}}</strong></td>
          <td>
            @if($data->photo)
              <img src="{{ asset($data->photo) }}" alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            @else
              <span class="text-muted">No Photo</span>
            @endif
          </td>
          <td>{{$data->full_name}}</td>
          <td>{{$data->mobile}}</td>
          <td>{{$data->department ? $data->department->name : 'N/A'}}</td>
          <td>{{$data->designation ? $data->designation->name : 'N/A'}}</td>
          <td>{{date('d-m-Y',strtotime($data->joining_date))}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->employment_status] }}">{{config('myhelpers.status')[$data->employment_status]}}</strong></td>
          <td>
           {!! HTML::decode(link_to_route('permanent_employees.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('permanent_employees.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
