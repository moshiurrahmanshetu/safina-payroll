@extends('layouts.admin')
@section('title', 'Purchased Stock In List')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-2"><h3>Purchased Stock In List </h3></div>
  <div class="col-sm-12 col-md-8"><h3>
    <form action="" method="GET" role="search" >
    <table class="table table-borderless">
        <tr>
          <?php if($search_array['start_date']){ $s_date=date('d-m-Y',strtotime($search_array['start_date'])); }else{ $s_date=''; }
          if($search_array['end_date']){ $e_date=date('d-m-Y',strtotime($search_array['end_date'])); }else{ $e_date=''; } ?>
          <td> {{Form::text('start_date',$s_date,array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
           <td> {{Form::text('end_date',$e_date,array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))}} </td>
          <td> 
            {{Form::select('item_id',array(''=>'Select Item Name')+$items,$search_array['item_id'],array('class' => 'form-control'))}}  
          </td>
          <td> 
            {{Form::select('supplier_id',array(''=>'Select Supplier Name')+$suppliers,$search_array['supplier_id'],array('class' => 'form-control'))}}
          </td> 
           <td><span class="input-group-btn">   
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit">
              <span class="fa fa-search"></span>
            </button>
            </span>
          </td>
          @if($search_array['supplier_id']!='')
           <td> 
            <!-- <a class="btn btn-info pull-right txt_white" target="_blank" href="{{ route('stock_in_print',['download'=>'stock_in','item_id'=>$search_array['item_id'],'supplier_id'=>$search_array['supplier_id'],'start_date'=>$search_array['start_date'],'end_date'=>$search_array['end_date']]) }}">Print</a> -->
          </td>
          @endif
       </tr>
      </table> 
    </form> </h3>
  </div>
  <div class="col-sm-12 col-md-2"><h1>
  {{link_to_route('stock_in.create','Create New',[], array('class'=>'btn btn-success pull-right'))}} </h1>
  </div>  
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <td>#</td>
            <th>Product Name</th>
            <th width="10%">Stock Date</th>
            <th>Quantity</th>
            <th>Received By</th>
            <th>Received From</th>
            <th>Departments</th>
            <th>Stock Location</th>
            <th>Updated By</th>
            <th>Updated At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; $total_qty=0; @endphp  
          @foreach ($stock_ins as $data)
          @php $total_qty+=$data->quantity; $combinations=json_decode($data->combinations, true); @endphp
          <tr>
            <td>{{$i}}</td>
            <td>{!!$data->item->name!!} <br>
            @if($combinations!='')
              @foreach($combinations as $key => $values)
                <b>{{$key}}</b>: {{$values}} <br>
              @endforeach
            @endif
            </td>
            <td>{{date('d-m-Y',strtotime($data->stock_date))}}</td>
            <td>{{$data->quantity+0}} {!!$data->item->measuring_unit!!} </td>
            <td>{!!$received_by[$data->received_by]!!}</td>
            <td>{{$data->given_by}}</td>
            <td>{{$departments[$data->department_id]}}</td>
            <td>{{$data->warehouse->name}}</td>
            <td>{!!$received_by[$data->updated_by]!!}</td>
            <td>{{date('d-m-Y',strtotime($data->updated_at))}}</td>
            <td>
              {!! HTML::decode(link_to_route('stock_in.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
              {{ Form::open(array('route' => array('stock_in.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
              <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
              {{ Form::close() }}
            </td>
          </tr>
          @php $i=$i+1; @endphp
          @endforeach
        </tbody>
        <thead>
        <tr>
          <th colspan='11' class="txt_white_back_green" > Total Quantity: {{$total_qty}}</th> 
        </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection