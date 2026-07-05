@extends('layouts.admin')
@section('title', 'Purchase Create')
@section('content')
<h3 class="page-header">New Purchase Create {{link_to_route('purchase.index','Purchase List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
 {{ Form::model(Request::old(),array('route' => array('purchase.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
	<div class="row">
		<div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Supplier Type <sup>*</sup></label>
          {{Form::select('supplier_type',config('myhelpers.supplier_type'),null,array('class' => 'form-control', 'required'=>'required','onChange'=>'show_supplier_names(this.value)'))}}  
          {!! $errors->first('supplier_type', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Supplier Name <sup>*</sup></label>
          {{Form::select('supplier_id',array(''=>'Select Supplier Name'),null,array('class' => 'form-control','id'=>'supplier_lists','onChange'=>'show_supllier_info(this.value)'))}}
          {!! $errors->first('supplier_id', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Mobile No <sup>*</sup></label>
          {{Form::text('mobile',null,array('class' => 'form-control nullItem', 'required'=>'required'))}}
          {!! $errors->first('mobile', '<p class="text-danger">:message</p>' ) !!}
          {{Form::hidden('contact_name',null,array('class' => 'nullItem'))}}
          {{Form::hidden('company_name',null,array('class' => 'nullItem'))}}
          {{Form::hidden('address',null,array('class' => 'nullItem'))}}
          {{Form::hidden('email',null,array('class' => 'nullItem'))}}
          {{Form::hidden('web_site',null,array('class' => 'nullItem'))}}
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column"> 
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Purchase Order No </label>
          {{Form::text('po_number',null, array('class' => 'form-control'))}}    
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Purchase Person <sup>*</sup> </label>
          {{Form::select('purchase_person',array(''=>'Select Person ')+$purchase_persons,null,array('class' => 'form-control', 'required'=>'required'))}} 
          {!! $errors->first('purchase_person', '<p class="text-danger">:message</p>' ) !!}  
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Purchase Date <sup>*</sup></label>
          {{Form::text('purchase_date',null, array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'required'=>'required'))}}
          {!! $errors->first('purchase_date', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div> 
    </div>
    <div class="col-md-12 multi-column"> 
      <div>
      <div class=""><label for=""><h3>Item Details:</h3></label></div>
      <table class="table table-bordered table-hover" id="salescount">
        <thead>
          <tr>
            <th width="25%" class="text-center">
              Select Product Name
            </th>
            <th width="30%" class="text-center">
              Description
            </th>
            <th width="15%" class="text-center">
              Unit Price
            </th>
            <th width="10%" class="text-center">
              QTY
            </th>
            <th width="10%" class="text-center">
              Add/Remove
            </th>
          </tr>
        </thead>
        <tbody>
          <tr class="calculate-row" id="0_info" row-id='0'>
            <td>
            {{Form::select('activity[0][item_name]', array(''=>'Select Product Name')+$item_names, null, array('class' => 'form-control', 'required', 'onChange'=>'show_item_details(this.value, 0)'))}}<br>
            <span id="attri_0"></span>
            {{Form::hidden('activity[0][category_id]',null)}}
            {{Form::hidden('activity[0][name]',null)}}
            </td>
            <td>
            {{Form::textarea('activity[0][description]',null, array('class' => 'form-control Item_value_0', 'placeholder'=>'Description', 'rows'=>4))}}
            </td>
            <td>
            {{Form::number('activity[0][unit_price]',null, array('class' => 'form-control calculate_unit_price5','placeholder'=>'Unit Price','step'=>'any'))}}
            </td>
            <td>
            {{Form::number('activity[0][no_of_unit]',1, array('class' => 'form-control calculate_no_of_unit5', 'placeholder'=>'No of unit','step'=>'any'))}}<br>
            {{Form::text('activity[0][measuring_unit]',null, array('class' => 'form-control Item_value_0', 'readonly'=>'readonly'))}}
            </td>
            <td>
            <button type="button"  onclick="addMore('0_info')" class="btn btn-success btn-sm">
              <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
            </td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>
    <hr>
    <div class="form-group">
      <div class="col-md-offset-1">
        <div class='col-md-10 well'>
          <span><b>Special Instructions :</b> </span>
          {{Form::text('special_instruction',null,array('class' => 'form-control','id'=>'special_instruction'))}}
        </div>
      </div>  
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Status</label>
          {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}     
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">            
          <button type="submit" class="btn btn-primary">
            Create Purchase
          </button>
        </div>
      </div>
    </div>
</div>
 <?php $bac =''; 
    if($item_names){
      foreach ($item_names as $key => $value) {
        $bac.= '<option value="'.$key.'">'.addslashes($value).'</option>';
      }
    }
    
  ?>
{{ Form::close() }}
@endsection
@section('script')
<script>
  var addMore = (function () 
  {   
    var id = 1; var rawid=0;
    return function (previous_id) {
      $('.calculate-row').each(function (){
        rawid=$(this).attr('row-id'); rawid=parseInt(rawid);
        if(rawid>=id){ id=rawid+1; }
    });
   
    var item_option= '<?php echo $bac; ?>';
      var myvar = '<tr id="'+id+'_info" class="calculate-row" row-id="'+id+'">'+
      '           <td>'+
      '           <select class="form-control" required="" name="activity['+id+'][item_name]" onchange="show_item_details(this.value, '+id+')"><option value="" selected="selected">Select Item Name</option>'+item_option+'</select>'+
      '           <span id="cat_name_'+id+'"></span><br><span id="attri_'+id+'"></span><input type="hidden" name="activity['+id+'][category_id]"/><input name="activity['+id+'][name]" type="hidden">'+
      '           </td>'+
      '           <td>'+
      '           <textarea type="text" name="activity['+id+'][description]" placeholder="description" class="form-control Item_value_'+id+'" rows="4"></textarea>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity['+id+'][unit_price]" placeholder="Unit Price" class="form-control calculate_unit_price5" step="any" required/><span id="reg_'+id+'"></span><br><span id="sale_'+id+'"></span>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity['+id+'][no_of_unit]" value="1" placeholder="no of unit" class="form-control calculate_no_of_unit5" min="0" step="any"/>'+
      '           <br><input type="text" name="activity['+id+'][measuring_unit]" class="form-control Item_value_'+id+'" readonly="readonly"/>'+
      '           </td>'+
      '           <td>'+
      '           <button type="button" onclick="addMore(\''+id+'_info\')" class="btn btn-success btn-sm abc">'+
      '           <i class="fa fa-plus" aria-hidden="true"></i>'+
      '           </button>'+
      '           <button type="button" onclick="remove(\''+id+'_info\')" class="btn btn-danger btn-sm">'+
      '            <i class="fa fa-minus" aria-hidden="true"></i>'+
      '           </button>'+
      '           </td>'+
      '         </tr>';

      $('#'+previous_id).after(myvar);
      };
  })();
  function remove(id){
      $('#'+id).remove();
  }
  // =======add more option for Purchase create end ============= 

</script>
@endsection
