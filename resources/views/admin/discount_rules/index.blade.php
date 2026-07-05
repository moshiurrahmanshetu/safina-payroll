@extends('layouts.admin')
@section('title', 'Promo Discount List')
@section('content')
<h3 class="page-header">Promo Discount List {{link_to_route('discount_rules.create','Add Promo Discount',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Code</th>
            <th>Discount</th>
            <th>Service</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($discount_rules as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->name}}</td>
          <td>{{$data->code ? $data->code : '-'}}</td>
          <td>{{$data->amount}} {{$data->discount_type == 1 ? '%' : '(Fixed)'}}</td>
          <td>{{$data->service ? $data->service->name : 'All Services'}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
            {!! HTML::decode(link_to_route('discount_rules.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
            {{ Form::open(array('route' => array('discount_rules.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form-'.$data->id)) }}
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
