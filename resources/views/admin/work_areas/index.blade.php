@extends('layouts.admin')
@section('title', 'Work Area List')
@section('content')
<h3 class="page-header">Work Area List @if($work_areas) ({{count($work_areas)}}) @endif {{link_to_route('work_areas.create','Add Work Area',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'work_areas.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Work Area Name">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>Status:</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('work_areas.index') }}" class="btn btn-danger">Reset</a>
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
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($work_areas as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->name}}</strong></td>
          <td>{{$data->description ?? 'N/A'}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
           {!! HTML::decode(link_to_route('work_areas.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('work_areas.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
