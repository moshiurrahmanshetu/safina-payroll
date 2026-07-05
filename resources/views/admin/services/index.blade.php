@extends('layouts.admin')
@section('title', 'Service List')
@section('content')
<h3 class="page-header">Service List @if($services) ({{count($services)}}) @endif {{link_to_route('services.create','Add Service',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Pricing Type</th>
            <th>Bookings</th>
            <th>Revenue</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($services as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->name}}</td>
          <td>{{config('myhelpers.pricing_type')[$data->pricing_type]}}</td>
          <td>{{$data->total_bookings ?? 0}}</td>
          <td>৳{{number_format($data->total_revenue ?? 0, 2)}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
            {!! HTML::decode(link_to_route('services.show', '<i class="nav-icon icon-eye"></i>', array($data->id)))!!}
            {!! HTML::decode(link_to_route('services.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
            {{ Form::open(array('route' => array('services.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form-'.$data->id)) }}
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
