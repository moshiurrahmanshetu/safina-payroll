@extends('layouts.admin')
@section('title', 'Update Purchase')
@section('content')
<h3 class="page-header">Update Purchase {{link_to_route('purchase.index',' Purchase List',null,array('class'=>'btn btn-success pull-right'))}} </h3>
{{ Form::model($purchases,array('route' => array('purchase.update', $purchases->id), 'class'=>'form-horizontal', 'method' => 'PUT')) }} 
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label for="account_type" class="control-label">Supplier Type <sup>*</sup></label>
        {{Form::select('supplier_type',config('myhelpers.supplier_type'),null,array('class' => 'form-control', 'required'=>'required','onChange'=>'show_supplier_names(this.value)'))}}
        {!! $errors->first('supplier_type', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Supplier Name <sup>*</sup></label>
        {{Form::select('supplier_id',array(''=>'Select Supplier Name')+$supllier_lists,null,array('class' => 'form-control','id'=>'supplier_lists', 'required'=>'required','onChange'=>'show_supllier_info(this.value)'))}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Contact Name <sup>*</sup></label>
        {{Form::text('contact_name',null,array('class' => 'form-control nullItem', 'required'=>'required'))}}
        {!! $errors->first('contact_name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div> 
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Company Name </label>
        {{Form::text('company_name',null,array('class' => 'form-control nullItem'))}}
      </div>
    </div> 
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Address </label>
        {{Form::text('address',null,array('class' => 'form-control nullItem'))}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Mobile No <sup>*</sup></label>
        {{Form::text('mobile',null,array('class' => 'form-control nullItem', 'required'=>'required'))}}
        {!! $errors->first('mobile', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column"> 
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Email </label>
        {{Form::text('email',null,array('class' => 'form-control nullItem'))}}
      </div>
    </div>    
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Web Site </label>
        {{Form::text('web_site',null,array('class' => 'form-control nullItem'))}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="account_type" class="control-label">Invoice No</label>
        {{Form::text('invoice_no',null, array('class' => 'form-control'))}}
      </div>
    </div>
  </div> 
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Purchase Date <sup>*</sup></label>
        <?php if($purchases->purchase_date){ $p_date=date('d-m-Y',strtotime($purchases->purchase_date)); }else{ $p_date=''; } ?>
        {{Form::text('purchase_date',$p_date, array('class' => 'form-control datetimepicker1', 'required'=>'required'))}}
        {!! $errors->first('purchase_date', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div> 
    <div class="col-md-4">
      <div class="form-group">
        <label for="account_type" class="control-label">Purchase Order No </label>
        {{Form::text('po_number',null, array('class' => 'form-control'))}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Delivery Point </label>
        {{Form::text('fob_point',null, array('class' => 'form-control'))}}
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
              Item Category
            </th>
            <th width="20%" class="text-center">
              Select Item Name
            </th>
            <th width="20%" class="text-center">
              Description
            </th>
            <th width="12%" class="text-center">
              Unit Price
            </th>
            <th width="10%" class="text-center">
              QTY
            </th>
            <th width="10%" class="text-center">
              Total TK
            </th>
            <th width="10%" class="text-center">
              Add/Remove
            </th>
          </tr>
        </thead>
        <tbody>
          @php $count = 0; $att_val=array(); $att=array(); @endphp
          @foreach($purchases->purchase_items as $key=>$value)
          <tr class="calculate-row" id="{{$count}}_info" row-id='{{$count}}'>
            <td>
              {{Form::select('activity['.$count.'][type]',array(''=>'Select Item Type')+$cat_names, $value->category_id, array('class' => 'form-control', 'onChange'=>'show_type_wise_item_list(this.value, '.$count.')'))}}
            </td>
            <td>
            {{Form::select('activity['.$count.'][item_name]',array(''=>'Select Product Name')+$item_names, $value->item_id, array('class' => 'form-control', 'required','onChange'=>'show_item_details(this.value, '.$count.')'))}}
            {{Form::hidden('activity['.$count.'][name]',$value->name)}}
            {{Form::hidden('activity['.$count.'][id]',$value->id)}}
            <span id="attri_{{$count}}">
            @php
              $attributes=json_decode($value->item->attributes, true);
              $att=json_decode($value->combinations, true); 
            @endphp
            @foreach($attributes as $key1=>$value1)
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
              {{Form::select('activity1['.$count.']['.$att_name.']',array(''=>'Select '.$att_name)+$att_val, $att,array('class' => 'form-control combination_'.$count))}}
              @endforeach
            </span>
            </td>
            <td>
            {{Form::textarea('activity['.$count.'][description]',$value->description,array('class' => 'form-control', 'placeholder'=>'Description', 'rows'=>4))}}
            </td>
            <td>
            {{Form::number('activity['.$count.'][unit_price]',$value->unit_price+0, array('class' => 'form-control calculate_unit_price1', 'placeholder'=>'Unit Price','step'=>'any','required'=>'required'))}}
            </td>
            <td>
            {{Form::number('activity['.$count.'][no_of_unit]',$value->quantity+0, array('class' => 'form-control calculate_no_of_unit1', 'placeholder'=>'No of unit','step'=>'any'))}}
            <br>
            {{Form::text('activity['.$count.'][measuring_unit]',$value->measuring_unit,array('class' => 'form-control', 'readonly'=>'readonly'))}}
            </td>
            <td>
              {{Form::number('activity['.$count.'][per_total]',$value->per_total+0, array('class' => 'form-control','readonly','placeholder'=>'0','step'=>'any'))}}
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
    <div class='col-md-12 well'>
      <div class="col-md-7 pull-left"></div>
      <div class="col-md-5 pull-right">
        <span><b>SUBTOTAL <sup>*</sup> :</b> </span>
        {{Form::number('sub_total',null,array('class' => 'form-control','readonly','id'=>'sub_total','step'=>'any', 'required'=>'required'))}}
        <br>
        <br>
        <span><b>Discount :</b> </span>
        {{Form::number('discount',null,array('class' => 'form-control','id'=>'discount1','step'=>'any'))}}
        <span><b>VAT * (<select class="selectvatbox1" required="" name="vat_percent">
            <option value="0">0 %</option>
            <option <?php if($purchases->vat_percent=='5.00'){ echo "selected";} ?> value="5">5 %</option>
            <option <?php if($purchases->vat_percent=='15.00'){ echo "selected";} ?> value="15">15 %</option>
          </select>) :</b> </span>
        {{Form::number('vat',null,array('class' => 'form-control','id'=>'total_vat','readonly','step'=>'any', 'required'=>'required'))}}
        <br>          
        <span><b>GRAND TOTAL <sup>*</sup> :</b> </span>
        {{Form::number('grand_total',null,array('class' => 'form-control','id'=>'total_price','readonly','step'=>'any', 'required'=>'required'))}}
      </div>
    </div>      
  </div>

  <div class="form-group">
    <div class="col-md-offset-1">
      <div class='col-md-10 well'>
        <span><b>Amount In Word <sup>*</sup> :</b> </span>
        {{Form::text('inword',null,array('class' => 'form-control','id'=>'inword', 'required'=>'required'))}}
        <br>
        <span><b>Special Instructions :</b> </span>
        {{Form::text('special_instruction',null,array('class' => 'form-control','id'=>'special_instruction'))}}
      </div>
    </div>  
  </div>
  <div class="col-md-12 multi-column">
     <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Purchase Person <sup>*</sup> </label>
        {{Form::select('purchase_person',array(''=>'Select Person ')+$purchase_persons,null,array('class' => 'form-control', 'required'=>'required'))}} 
        {!! $errors->first('purchase_person', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="account_type" class="control-label">Purchase Status</label>
        {{Form::select('status',config('myhelpers.purchase_status'),null,array('class' => 'form-control'))}}     
      </div>
    </div>
  </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">            
          <button type="submit" class="btn btn-primary">
            Update Purchase
          </button>
        </div>
      </div>
    </div>
</div>
{{ Form::close() }}
@endsection
@section('script')
<script>
   // =======add more option for Purchase create start ============= 
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
          $bac.= '<option value="'.$key.'">'.$value.'</option>';
        }
      }
      ?>
      var item_option= '<?php echo $bac; ?>';
      <?php $types =''; 
      if($cat_names){
        foreach ($cat_names as $key => $value) {
          $types.= '<option value="'.$key.'">'.$value.'</option>';
        }
      }
      ?>
      var type_option= '<?php echo $types; ?>';
      //alert(id);
      var myvar = '<tr id="'+id+'_info" class="calculate-row" row-id="'+id+'">'+
      '           <td>'+
      '        <select class="form-control" onchange="show_type_wise_item_list(this.value, '+id+')" name="activity['+id+'][type]"><option value="" selected="selected">Select Item Type</option>'+type_option+'</select>'+
      '     </td><td>'+
      '           <select class="form-control" required="" name="activity['+id+'][item_name]" onchange="show_item_details(this.value, '+id+')"><option value="" selected="selected">Select Item Name</option>'+item_option+'</select><input name="activity['+id+'][name]" type="hidden"><span id="attri_'+id+'"></span>'+
      '           </td>'+
      '           <td>'+
      '           <textarea type="text" name="activity['+id+'][description]" placeholder="description" class="form-control", "rows"=3></textarea>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity['+id+'][unit_price]" placeholder="unit Price" class="form-control calculate_unit_price1" step="any"/>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity['+id+'][no_of_unit]" value="1" placeholder="no of unit" class="form-control calculate_no_of_unit1" min="0" step="any"/><br><input type="text" name="activity['+id+'][measuring_unit]" class="form-control" readonly="readonly"/>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity['+id+'][per_total]" readonly placeholder="total" class="form-control" step="any"/>'+
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
      calculate1();
    };
  })();
  function remove(id){
    $('#'+id).remove();
    calculate1();
      //alert('hello');
  }
  // =======add more option for Purchase create end ============= 

</script>
@endsection