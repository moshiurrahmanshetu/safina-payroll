@extends('layouts.admin')
@section('title', 'Update Requisition')
@section('content')
<h3 class="page-header">Update Requisition {{link_to_route('requisition.index',' Requisition List',null,array('class'=>'btn btn-success pull-right'))}} </h3>
{{ Form::model($requisitions,array('route' => array('requisition.update', $requisitions->id), 'class'=>'form-horizontal', 'method' => 'PUT')) }} 
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
          {{Form::select('purpose_id',array(''=>'Select Purpose Name')+$purpose,null, array('class' => 'form-control', 'required'=>'required'))}}
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
      <div class=""><label for=""><h3>Item Details: <strong class="btn-{{ config('myhelpers.status_color')[$requisitions->status] }}">({{config('myhelpers.requisition_status')[$requisitions->status]}})</strong></h3></label></div>
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
          @php $count = 0; $att_val=array(); $att=array(); @endphp
          @foreach($requisitions->requisition_items as $key=>$value)
          <tr class="calculate-row" id="{{$count}}_info" row-id='{{$count}}'>
            @php
              $attributes=json_decode($value->item->attributes, true);
              $att=json_decode($value->combinations, true);
            @endphp
            <td>
              {{Form::select('activity['.$count.'][type]',array(''=>'Select Item Type')+$item_types, $value->category_id, array('class' => 'form-control', 'onChange'=>'show_type_wise_item_list(this.value, '.$count.')'))}}
            </td>
            <td>
            {{Form::select('activity['.$count.'][item_name]',array(''=>'Select Product Name')+$item_names, $value->item_id, array('class' => 'form-control', 'required','onChange'=>'show_item_details(this.value, '.$count.')'))}}
            {{Form::hidden('activity['.$count.'][name]',$value->name)}}
            {{Form::hidden('activity['.$count.'][id]',$value->id)}}
            <span id="attri_{{$count}}">
              @foreach ($attributes as $key1=>$value1)
                @php
                  $values=$value1['values'];
                  $op=explode('|',$values); $att_op=array();
                  if ($op) {
                    foreach($op as $items){
                      $att_op[$items]=$items;
                    }
                  }
                  $att_val=$att_op; $att_name=$value1["name"];
                @endphp
                {{Form::select('activity1['.$count.']['.$att_name.']',array(''=>'Select '.$att_name)+$att_val, $att,array('required', 'class' => 'form-control combination_'.$count))}}
                @endforeach
            </span>
            </td>
            <td>
            {{Form::textarea('activity['.$count.'][description]',$value->description,array('class' => 'form-control', 'placeholder'=>'Description', 'rows'=>4))}}
            </td>
            <td>
            {{Form::number('activity['.$count.'][req_quantity]',$value->req_quantity,array('class' => 'form-control', 'placeholder'=>'No of unit','step'=>'any'))}}
            <br>
            {{Form::text('activity['.$count.'][measuring_unit]',$value->measuring_unit,array('class' => 'form-control', 'readonly'=>'readonly'))}}
            </td>
            <td>
            @if($loop->last)
            <button type="button"  onclick="addMore('{{$count}}_info')" class="btn btn-success btn-sm">
            <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
            @endif
            </td>
          </tr>
          @php $count++; @endphp
          @endforeach
        </tbody>
      </table>
      </div>
    </div>
    <hr>
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">
          <button type="submit" class="btn btn-primary">
            Update Requisition
          </button>
        </div>
      </div>
    </div>
</div>
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
      <?php $bac =''; 
      if($item_names){
        foreach ($item_names as $key => $value) {
          $bac.= '<option value="'.$key.'">'.addslashes($value).'</option>';
        }
      }
      ?>
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
      '           <span id="cat_name_'+id+'"></span><br><span id="attri_'+id+'"></span><br><input type="hidden" name="activity['+id+'][category_id]" /><input name="activity['+id+'][name]" type="hidden"><input name="activity['+id+'][created_at]" type="hidden">'+
      '           </td>'+
      '           <td>'+
      '           <textarea type="text" name="activity['+id+'][description]" placeholder="description" class="form-control" rows="4"></textarea>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity['+id+'][req_quantity]" value="1" placeholder="no of unit" class="form-control" min="0" step="any"/>'+
      '           <br><input type="text" name="activity['+id+'][measuring_unit]" class="form-control" readonly="readonly"/>'+
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
</script>
@endsection