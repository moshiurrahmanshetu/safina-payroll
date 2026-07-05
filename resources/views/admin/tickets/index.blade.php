@extends('layouts.admin')
@section('title', 'Ticket List')
@section('content')
<h3 class="page-header">Ticket List @if($tickets) ({{count($tickets)}}) @endif {{link_to_route('tickets.create','Add Ticket',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Ticket Name</th>
            <th>Price</th>
            <th>Status</th>
            <th>Updated By</th>
            <th>Updated At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($tickets as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->name}}</td>
          <td>{{$data->price}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>{!!$users[$data->updated_by]!!}</td>
          <td>{{date('d-m-Y',strtotime($data->updated_at))}}</td>
          <td>
           {!! HTML::decode(link_to_route('tickets.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('tickets.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
