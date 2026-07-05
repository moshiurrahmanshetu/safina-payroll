@extends('layouts.admin')
@section('title', 'Service Category List')
@section('content')
<h3 class="page-header">Service Category List {{link_to_route('service_categories.create','Add Service Category',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Category Name</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($categories as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->name}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
           {!! HTML::decode(link_to_route('category_meta_fields.create', '<i class="nav-icon icon-list"></i>', array($data->id), array('class'=>'btn btn-info', 'title'=>'Customer Information Fields')))!!}
           {!! HTML::decode(link_to_route('service_categories.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('service_categories.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
