@extends('layouts.admin')
@section('title', 'Time Slot List')
@section('content')
<h3 class="page-header">Time Slot List {{link_to_route('time-slots.create','Add Time Slot',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Service</th>
            <th>Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($time_slots as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->service ? $data->service->name : 'N/A'}}</td>
          <td>{{$data->name}}</td>
          <td>{{date('h:i A', strtotime($data->start_time))}}</td>
          <td>{{date('h:i A', strtotime($data->end_time))}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
            {!! HTML::decode(link_to_route('time-slots.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
            {{ Form::open(array('route' => array('time-slots.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form-'.$data->id)) }}
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
