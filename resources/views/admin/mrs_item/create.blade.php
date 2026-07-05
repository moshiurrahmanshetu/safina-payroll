@extends('layouts.admin')
@section('title', 'MRS Item Receive')
@section('css')
<link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
<h3 class="page-header">MRS Item Receive {{link_to_route('mrs_item.index','MRS Item List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
 {{ Form::model(Request::old(),array('route' => array('mrs_item.store'),'class'=>'form-horizontal')) }}
	<div class="row">
		<div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Receive From <sup>*</sup></label>
          {{Form::select('user_id',array(''=>'Select User Name')+$users, null, array('class' => 'form-control select-search', 'required','onChange'=>'show_user_item_mrs(this.value)'))}}
          {!!$errors->first('user_id', '<p class="text-danger">:message</p>')!!}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Product Name <sup>*</sup></label>
          {{Form::select('item_id', array(''=>'Select Product Name'), null, array('class' => 'form-control', 'required', 'id'=>'item_list', 'onChange'=>'show_mrs_item_details(this.value)'))}}
          {!!$errors->first('item_id', '<p class="text-danger">:message</p>')!!}
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <label class="control-label">Requisition Details</label>
          {{Form::textarea('requisition_details',null,array('class' => 'form-control', 'rows'=>'4', 'readonly'))}}
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received By <sup>*</sup></label>
          {{Form::select('received_by',$receive_by,null,array('class' => 'form-control', 'required'=>'required'))}} 
          {!! $errors->first('received_by', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received Quantity <sup>*</sup></label>
          {{Form::number('quantity',null,array('class' => 'form-control', 'placeholder'=>'qty','step'=>'any', 'required'=>'required'))}}
          {!! $errors->first('quantity', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div> 
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Item Condition <sup>*</sup></label>
          {{Form::select('item_condition',config('myhelpers.item_condition'),null,array('class' => 'form-control', 'required'=>'required'))}}
          {!! $errors->first('item_condition', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received Date <sup>*</sup></label>
          {{Form::text('received_date',null, array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'required'=>'required'))}}
          {!! $errors->first('received_date', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>      
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Stock Warehouse Location <sup>*</sup></label>
          {{Form::select('warehouse_id',array(''=>'Select Warehouse')+$warehouses,null,array('class' => 'form-control', 'required'=>'required'))}} 
          {!! $errors->first('warehouse_id', '<p class="text-danger">:message</p>' ) !!}  
        </div>
      </div> 
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Admin Comments </label>
          {{Form::textarea('admin_comments',null, array('class' => 'form-control', 'rows'=>'3'))}}   
        </div>
      </div> 
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">            
          <button type="submit" class="btn btn-primary">
            MRS Item Receive
          </button>
        </div>
      </div>
    </div>
</div>

{{ Form::close() }}
@endsection
@section('script')
<script src="{{asset('public/js/select2.min.js')}}"></script>
<script>
  $(document).ready(function() {
    $('.select-search').select2();
  });
</script>
@endsection