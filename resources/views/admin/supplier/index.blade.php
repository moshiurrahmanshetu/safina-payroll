@extends('layouts.admin')
@section('title', 'Supplier List')
@section('content')
<h3 class="page-header">Supplier List {{link_to_route('supplier.create','Add Supplier',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <th>#</th>
            <th>Contact Name</th>
            <th>Company Name</th>
            <th>Company Type</th>
            <th>Mobile No</th>
            <th>Address</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; $total_amount=0; $total_paid=0; @endphp 
         @foreach ($suppliers as $data) 
         @php  $total_amount=0; $total_paid=0; $bill_amount=0; @endphp  
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->contact_name}}</td>
          <td>{{$data->company_name}}</td> 
          <td>
           {{config('myhelpers.supplier_type')[$data->supplier_type]}}</td> 
           <td>{{$data->mobile}}</td> 
           <td>{{$data->address}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>

          <td> 
           {!!  HTML::decode(link_to_route('supplier.show', '<i class="fa fa-eye" style="font-size:15px"></i>', array($data->id)))!!} 
           {!!  HTML::decode(link_to_route('supplier.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('supplier.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
