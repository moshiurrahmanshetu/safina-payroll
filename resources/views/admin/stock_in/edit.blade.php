@extends('layouts.admin')
@section('title', 'Purchased Stock In')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-4"><h3>Purchased Stock In Edit</h3></div>
  <div class="col-sm-12 col-md-6"><h3>
     </h3>
  </div>
  <div class="col-sm-12 col-md-2"><h3 class="page-header">{{link_to_route('stock_in.index','Stock In List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
  </div>  
</div>

{{Form::model($stock_in,array('route' => array('stock_in.update', $stock_in->id), 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal', 'method' => 'PUT')) }}
  <div class="row">
    <div class="col-md-12 multi-column">              
      <div class="col-md-6">
        <div class="form-group">
          <label for="name" class="control-label"><b>Supplier Info:</b></label><br>
          Purchase Date: {{date('d-m-Y',strtotime($stock_in->purchase->purchase_date))}}<br>
          Supplier Name: {{$stock_in->purchase->contact_name}} ({{$stock_in->purchase->company_name}})<br>
          Mobile: {{$stock_in->purchase->mobile}}
        </div>
      </div>         
    </div>
    <div class="col-md-12 multi-column">      
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received By <sup>*</sup> </label>
          {{Form::select('received_by',array(''=>'Select Person')+$received_by,$stock_in->received_by,array('class' => 'form-control', 'required'=>'required'))}} 
          {!! $errors->first('received_by', '<p class="text-danger">:message</p>' ) !!}  
        </div>
      </div> 
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received From <sup>*</sup> </label>
          {{Form::text('given_by',null, array('class' => 'form-control', 'required'=>'required'))}}
          {!! $errors->first('given_by', '<p class="text-danger">:message</p>' ) !!}    
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Stock Date <sup>*</sup> </label>
          <?php if($stock_in->stock_date){ $stock_date=date('d-m-Y',strtotime($stock_in->stock_date)); }else{ $stock_date=''; } ?>
          {{Form::text('stock_date',$stock_date, array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'required'=>'required'))}}
          {!! $errors->first('stock_date', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div> 
    </div>
    <div class="col-md-12 multi-column"> 
      <div>
      <div class=""><label for=""><h3>Item Details:</h3></label></div>
      <table class="table table-bordered table-hover" id="salescount">
        <thead>
          <tr>
            <th width="2%">#</th>
            <th width="15%" class="text-center">Product Name</th>
            <th width="12%" class="text-center">Category</th>
            <th width="12%" class="text-center">Combination</th>
            <th width="16%" class="text-center">Unit Price</th>
            <th width="10%" class="text-center">Total QTY</th>
            <th width="12%" class="text-center">Already Stocked</th>
            <th width="11%" class="text-center">Update StockIn</th>
            <th width="10%" class="text-center">M.Unit</th>
          </tr>
        </thead>
        <tbody>
          @php $count = 0; $max=$stock_in->purchase_item->quantity-$total_qty+$stock_in->quantity; 
          @endphp
          <tr class="calculate-row" id="{{$count}}_info" row-id='{{$count}}'>
            @php $count++; $combinations=json_decode($stock_in->purchase_item->combinations, true); 
            @endphp
            <td>{{$count}}</td>
            <td>{{$stock_in->purchase_item->name}}</td>
            <td>{{$stock_in->purchase_item->category->name}}</td>
            <td>
            @if($combinations)
		@foreach($combinations as $key => $values)
            		<b>{{$key}}</b>: {{$values}} <br>
            	@endforeach
	    @endif
            </td>
            <td>{{$stock_in->purchase_item->unit_price+0}}<br>
              </td>
            <td>{{$stock_in->purchase_item->quantity+0}}</td> 
            <td>{{$total_qty+0}}</td>
            <td>
              {{Form::number('quantity',null,array('class' => 'form-control', 'max'=>$max, 'placeholder'=>'qty','step'=>'any'))}}
            </td>
            <td>{{$stock_in->purchase_item->measuring_unit}}</td>
          </tr>          
        </tbody>
      </table>
      </div>
    </div>

    <div class="col-md-12 multi-column">      
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Stock Warehouse Location <sup>*</sup> </label>
          {{Form::select('warehouse_id',array(''=>'Select Warehouse')+$warehouses,null,array('class' => 'form-control', 'required'=>'required'))}} 
          {!! $errors->first('warehouse_id', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div> 
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Remarks </label>
          {{Form::textarea('remarks',null, array('class' => 'form-control', 'rows'=>'2'))}}   
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group pull-right"> <br>
          <button type="submit" class="btn btn-primary pull-right">Update Stock</button>
        </div>
      </div> 
    </div>

  </div>
{{ Form::close() }}
@endsection
