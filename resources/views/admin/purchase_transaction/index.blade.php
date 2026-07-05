@extends('layouts.admin')
@section('title', 'Transaction List')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>Transaction List </h3></div>
  <div class="col-sm-12 col-md-7"><h3>
    <form action="" method="GET" role="search" >
    <table class="table table-borderless">
        <tr>
          <td> {{Form::text('start_date',$search_array['start_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
           <td> {{Form::text('end_date',$search_array['end_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))}} </td>
          <td> 
            {{Form::select('supplier_id',array(''=>'Select Supplier Name')+$supplier_list,$search_array['supplier_id'],array('class' => 'form-control'))}}
          </td> 
           <td><span class="input-group-btn">
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit">
              <span class="fa fa-search"></span>
            </button>
            </span>
          </td>
           @if($search_array['supplier_id']!='')
           <td> 
            
          </td>
          @endif
       </tr>
      </table> 
    </form> </h3>
  </div>
  <div class="col-sm-12 col-md-2"><h1>
  {{link_to_route('purchase.index','Purchase Lists',[],array('class'=>'btn btn-success pull-right'))}} </h1>
  </div>  
</div>
{{ session()->get('langsname') }}
<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <td>#</td>
            <th>Supplier Name</th>
            <th>Purchase Info</th>
            <th>Payment Type</th>
            <th>Receipt No.</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Print</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; $total_paid=0;@endphp
          @foreach ($purchase_transactions as $data)
          @php $total_paid+=$data->amount;@endphp
          <tr>
            <td>{{$i}}</td>
            <td width="15%">{!!$data->supplier->contact_name!!} ({{$data->supplier->company_name}})</td>
            <td>{!! HTML::decode(link_to_route('purchase.edit', '<i class="nav-icon icon-eye"></i>', array($data->purchase_id)))!!}</td>
            <td>{{config('myhelpers.payment_type')[$data->payment_type]}}</td>
          
            <td>{{$data->money_rceipt_no}}</td>
            <td>{{$data->amount}}</td>
            <td>{{date('d-m-Y',strtotime($data->payment_date))}}</td>
             <td> 
             <a href="{{route('purchase_transaction.show',$data->id)}}"><i class="nav-icon icon-printer"></i></a>
            </td> 
            <td>
              {!! HTML::decode(link_to_route( 'purchase_transaction.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
            </td> 
             <td>
              {{ Form::open(array('route' => array('purchase_transaction.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
              <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
              {{ Form::close() }}
            </td>
          </tr>
          @php $i=$i+1; @endphp
          @endforeach
        </tbody>
        <thead>
        <tr>
          <th colspan='11' class="txt_white_back_green"> Total Purchase = {{$purchases->total+0}}, Total Paid = {{ $total_paid+0 }}, Total Due = {{ $purchases->total-$total_paid }}</th>
        </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection