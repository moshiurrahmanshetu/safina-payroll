@extends('layouts.admin')
@section('title', 'Requisition Request')
@section('content')
<h3 class="page-header">New Requisition Request {{link_to_route('requisition.index','Requisition List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
 {{ Form::model(Request::old(),array('route' => array('requisition.store'),'class'=>'form-horizontal')) }}
	<div class="row">
		<div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Purpose Type <sup>*</sup></label>
          {{Form::select('purpose_type',config('myhelpers.purpose_type'),null,array('class' => 'form-control', 'required'=>'required','onChange'=>'show_purpose_names(this.value)'))}}
          {!!$errors->first('purpose_type', '<p class="text-danger">:message</p>')!!}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Purpose Name <sup>*</sup></label>
          {{Form::select('purpose_id',array(''=>'Select Purpose Name'),null,array('class' => 'form-control', 'required'=>'required'))}}
          {!!$errors->first('purpose_id', '<p class="text-danger">:message</p>')!!}
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <label class="control-label">Requisitioner Comments/Instructions</label>
          {{Form::textarea('requisitioner_comments',null,array('class' => 'form-control', 'rows'=>'2'))}}
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column"> 
      <div>
      <div class=""><label for=""><h3>Item Details:</h3></label></div>
      <table class="table table-bordered table-hover" id="salescount">
        <thead>
          <tr>
            <th width="10%" class="text-center">
              Product Category
            </th>
            <th width="25%" class="text-center">
              Select Product Name
            </th>
            <th width="30%" class="text-center">
              Description
            </th>
            <th width="10%" class="text-center">
              Req. QTY
            </th>
            <th width="10%" class="text-center">
              Add/Remove
            </th>
          </tr>
        </thead>
        <tbody>
          <tr class="calculate-row" id="0_info" row-id='0'>
            <td>
              {{Form::select('activity[0][type]',array(''=>'Select Item Type')+$item_types, null, array('class' => 'form-control', 'onChange'=>'show_type_wise_item_list(this.value, 0)'))}}
            </td>
            <td>
            {{Form::select('activity[0][item_name]', array(''=>'Select Product Name')+$item_names, null, array('class' => 'form-control', 'required', 'onChange'=>'show_item_details(this.value, 0)'))}}<br>
            <span id="attri_0"></span>
            {{Form::hidden('activity[0][name]',null)}}
            </td>
            <td>
            {{Form::textarea('activity[0][description]',null, array('class' => 'form-control Item_value_0', 'placeholder'=>'Description', 'rows'=>4))}}
            </td>
            <td>
            {{Form::number('activity[0][req_quantity]',1, array('class' => 'form-control', 'placeholder'=>'Quantity','step'=>'any'))}}<br>
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
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">            
          <button type="submit" class="btn btn-primary">
            Request Requisition
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
    <?php $types =''; 
      if($item_types){
        foreach ($item_types as $key => $value) {
          $types.= '<option value="'.$key.'">'.$value.'</option>';
        }
      }
      ?>
      var type_option= '<?php echo $types; ?>';
      var myvar = '<tr id="'+id+'_info" class="calculate-row" row-id="'+id+'">'+
      '           <td>'+
      '        <select class="form-control" onchange="show_type_wise_item_list(this.value, '+id+')" name="activity['+id+'][type]"><option value="" selected="selected">Select Item Type</option>'+type_option+'</select>'+
      '     </td><td>'+
      '           <select class="form-control" required="" name="activity['+id+'][item_name]" onchange="show_item_details(this.value, '+id+')"><option value="" selected="selected">Select Product Name</option>'+item_option+'</select>'+
      '           <span id="cat_name_'+id+'"></span><br><span id="attri_'+id+'"></span><input type="hidden" name="activity['+id+'][category_id]"/><input name="activity['+id+'][name]" type="hidden">'+
      '           </td>'+
      '           <td>'+
      '           <textarea type="text" name="activity['+id+'][description]" placeholder="description" class="form-control Item_value_'+id+'" rows="4"></textarea>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity['+id+'][req_quantity]" value="1" placeholder="Quantity" class="form-control calculate_no_of_unit5" min="0" step="any"/>'+
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
  // =======add more option for requisition create end ============= 

</script>
@endsection
