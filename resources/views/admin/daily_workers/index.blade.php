@extends('layouts.admin')
@section('title', 'Daily Worker List')
@section('content')
<h3 class="page-header">Daily Worker List @if($daily_workers) ({{count($daily_workers)}}) @endif {{link_to_route('daily_workers.create','Add Daily Worker',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'daily_workers.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, Worker ID, Mobile">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>Work Area:</label>
          <select name="work_area_id" class="form-control">
            <option value="">All Work Areas</option>
            @foreach($work_areas as $id => $name)
              <option value="{{ $id }}" {{ request('work_area_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('daily_workers.index') }}" class="btn btn-danger">Reset</a>
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
            <th>Worker ID</th>
            <th>Photo</th>
            <th>Full Name</th>
            <th>Mobile</th>
            <th>Work Area</th>
            <th>Daily Wage</th>
            <th>Joining Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($daily_workers as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->worker_id}}</strong></td>
          <td>
            @if($data->photo)
              <img src="{{ asset($data->photo) }}" alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            @else
              <span class="text-muted">No Photo</span>
            @endif
          </td>
          <td>{{$data->full_name}}</td>
          <td>{{$data->mobile}}</td>
          <td>{{$data->workArea ? $data->workArea->name : 'N/A'}}</td>
          <td>{{$data->daily_wage}}</td>
          <td>{{date('d-m-Y',strtotime($data->joining_date))}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
           {!! HTML::decode(link_to_route('daily_workers.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('daily_workers.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
