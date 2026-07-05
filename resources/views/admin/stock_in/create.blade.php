@extends('layouts.admin')
@section('title', 'Purchased Stock In')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-4"><h3>Purchased Stock In Create</h3></div>
  <div class="col-sm-12 col-md-6"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>              
          <td> 
            {{Form::select('supplier_id',array(''=>'Select Supplier Name')+$suppliers,$supplier_id, array('class' => 'form-control', 'required'=>'required'))}}    
          </td> 
          <td><span class="input-group-btn">   
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit"><span class="fa fa-search"></span>
            </button></span>
          </td>
        </tr>
      </table> 
    </form> </h3>
  </div>
  <div class="col-sm-12 col-md-2"><h3 class="page-header">{{link_to_route('stock_in.index','Stock In List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
  </div>  
</div>
<?php $i=1; //dd($purchases); ?>

@foreach($purchases as $items)
  <?php $yes_count=0; ?>
  @foreach($items->purchase_items as $stock_check)
    @if(array_key_exists($stock_check->id,$stocks))
    <?php $yes_count+=$stock_check->quantity-$stocks[$stock_check->id]; ?>
    @else
    <?php $yes_count+=$stock_check->quantity; ?>
    @endif
  @endforeach

@if($yes_count>0)
{{Form::model(Request::old(),array('route' => array('stock_in.store'), 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')) }}
	<div class="row" <?php if($i%2==1){ echo 'style="background-color: #fff;"'; } $i++;?>>
		<div class="col-md-12 multi-column">              
      <div class="col-md-6">
        <div class="form-group">
          <label for="name" class="control-label"><b>Supplier Info:</b></label><br>
          Purchase Date: {{date('d-m-Y',strtotime($items->purchase_date))}}<br>
          Supplier Name: {{$items->contact_name}} ({{$items->company_name}})<br>
          Mobile: {{$items->mobile}}
          {{Form::hidden('purchase_id',$items->id)}}
          {{Form::hidden('supplier_id',$supplier_id)}}
        </div>
      </div>            
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received By <sup>*</sup></label>
          {{Form::select('received_by',array(''=>'Select Person')+$received_by,null,array('class' => 'form-control', 'required'=>'required'))}} 
          {!! $errors->first('received_by', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received For <sup>*</sup></label>
          {{Form::select('department_id',array(''=>'Select Department','0'=>'For All Departments')+$departments,null,array('class' => 'form-control', 'required'=>'required'))}} 
          {!! $errors->first('department_id', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div> 
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received From <sup>*</sup></label>
          {{Form::text('given_by',null, array('class' => 'form-control', 'required'=>'required'))}}
          {!! $errors->first('given_by', '<p class="text-danger">:message</p>' ) !!}
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
            <th width="12%" class="text-center">Unit Price</th>
            <th width="10%" class="text-center">Total QTY</th>
            <th width="12%" class="text-center">Already Stocked</th>
            <th width="12%" class="text-center">New StockIn</th>
            <th width="12%" class="text-center">M.Unit</th>
          </tr>
        </thead>
        <tbody>
          @php $count = 0; @endphp
          @foreach($items->purchase_items as $value)
          <tr class="calculate-row" id="{{$count}}_info" row-id='{{$count}}' <?php if($i%2==1){ echo 'style="background-color: #F8F8F8;"'; } $i++;?>>
            @php $count++; $combinations=json_decode($value->combinations, true); @endphp
            <td>{{$count}}</td>
            <td>{{$value->name}}</td>
            <td>{{$value->category->name}}</td>
            <td>
            @if($combinations!='')
              @foreach($combinations as $key => $values)
                <b>{{$key}}</b>: {{$values}} <br>
              @endforeach
            @endif
            </td>
            <td>
              {{$value->unit_price+0}}
            </td>
            <td>{{$value->quantity+0}}</td> 
            <td>
              @if(array_key_exists($value->id,$stocks))
                {{$stocks[$value->id]+0}} 
              <?php $max=$value->quantity-$stocks[$value->id]; ?>
              @else
              <?php $max=$value->quantity; ?>
                0
              @endif
            </td>
            <td>
            @if($max==0)
              Done
            @else
              {{Form::number('activity['.$value->id.'][quantity]',$max+0,array('class' => 'form-control', 'max'=>$max, 'placeholder'=>'qty','step'=>'any'))}}
              {{Form::hidden('activity['.$value->id.'][purchase_item_id]',$value->id)}}
              {{Form::hidden('activity['.$value->id.'][item_id]',$value->item_id)}}
              {{Form::hidden('activity['.$value->id.'][combinations]',$value->combinations)}}
            @endif
            </td>
            <td>{{$value->measuring_unit}}</td>
          </tr>
          @endforeach
          
        </tbody>
      </table>
      </div>
    </div>

    <div class="col-md-12 multi-column">
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Stock Date <sup>*</sup></label>
          {{Form::text('stock_date',null, array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'required'=>'required'))}}
          {!! $errors->first('stock_date', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>      
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Stock Warehouse Location <sup>*</sup></label>
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
      <div class="col-md-2">
        <div class="form-group pull-right"> <br>
          <button type="submit" class="btn btn-primary pull-right">Add Stock</button>
        </div>
      </div> 
    </div>

  </div>
  <hr>
{{ Form::close() }}
@endif
@endforeach

@endsection
