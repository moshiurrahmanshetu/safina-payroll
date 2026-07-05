
<?php $__env->startSection('title', 'Purchase Create'); ?>
<?php $__env->startSection('content'); ?>
<h3 class="page-header">New Purchase Create <?php echo e(link_to_route('purchase.index','Purchase List',[],array('class'=>'btn btn-success pull-right'))); ?></h3>
<?php echo e(Form::model(Request::old(),array('route' => array('purchase.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal'))); ?>

<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label for="account_type" class="control-label">Supplier Type <sup>*</sup></label>
        <?php echo e(Form::select('supplier_type',config('myhelpers.supplier_type'),null,array('class' => 'form-control', 'required'=>'required','onChange'=>'show_supplier_names(this.value)'))); ?>

        <?php echo $errors->first('supplier_type', '<p class="text-danger">:message</p>' ); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Supplier Name </label>
        <?php echo e(Form::select('supplier_id',array(''=>'Select Supplier Name'),null,array('class' => 'form-control','id'=>'supplier_lists','onChange'=>'show_supllier_info(this.value)'))); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Contact Name <sup>*</sup></label>
        <?php echo e(Form::text('contact_name',null,array('class' => 'form-control nullItem', 'required'=>'required'))); ?>

        <?php echo $errors->first('contact_name', '<p class="text-danger">:message</p>' ); ?>

      </div>
    </div> 
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Company Name </label>
        <?php echo e(Form::text('company_name',null,array('class' => 'form-control nullItem'))); ?>

      </div>
    </div> 
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Address </label>
        <?php echo e(Form::text('address',null,array('class' => 'form-control nullItem'))); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Mobile No <sup>*</sup></label>
        <?php echo e(Form::text('mobile',null,array('class' => 'form-control nullItem', 'required'=>'required'))); ?>

        <?php echo $errors->first('mobile', '<p class="text-danger">:message</p>' ); ?>

      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column"> 
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Email </label>
        <?php echo e(Form::text('email',null,array('class' => 'form-control nullItem'))); ?>

      </div>
    </div>    
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Web Site </label>
        <?php echo e(Form::text('web_site',null,array('class' => 'form-control nullItem'))); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="account_type" class="control-label">Invoice No</label>
        <?php echo e(Form::text('invoice_no',null, array('class' => 'form-control'))); ?>

      </div>
    </div>
  </div> 
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Purchase Date <sup>*</sup></label>
        <?php echo e(Form::text('purchase_date',null, array('class' => 'form-control datetimepicker1', 'required'=>'required'))); ?>

        <?php echo $errors->first('purchase_date', '<p class="text-danger">:message</p>' ); ?>

      </div>
    </div> 
    <div class="col-md-4">
      <div class="form-group">
        <label for="account_type" class="control-label">Purchase Order No </label>
        <?php echo e(Form::text('po_number',null, array('class' => 'form-control'))); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Delivery Point </label>
        <?php echo e(Form::text('fob_point',null, array('class' => 'form-control'))); ?>

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
          <tr class="calculate-row" id="0_info" row-id='0'>
            <td>
              <?php echo e(Form::select('activity[0][type]',array(''=>'Select Item Type')+$item_types, null, array('class' => 'form-control', 'onChange'=>'show_type_wise_item_list(this.value, 0)'))); ?>

            </td>
            <td>
              <?php echo e(Form::select('activity[0][item_name]',array(''=>'Select Item Name')+$item_names, null, array('class' => 'form-control', 'required','onChange'=>'show_item_details(this.value, 0)'))); ?> 
              <span id="attri_0"></span>
              <?php echo e(Form::hidden('activity[0][name]',null)); ?>

            </td>
            <td>
              <?php echo e(Form::textarea('activity[0][description]',null,array('class' => 'form-control Item_value_0', 'placeholder'=>'Description', 'rows'=>3))); ?>

            </td>
            <td>
              <?php echo e(Form::number('activity[0][unit_price]',null,array('class' => 'form-control calculate_unit_price1 Item_value_0','placeholder'=>'Unit Price','step'=>'any'))); ?>

            </td>
            <td>
              <?php echo e(Form::number('activity[0][no_of_unit]',1,array('class' => 'form-control calculate_no_of_unit1 Item_value_0','placeholder'=>'No of unit','step'=>'any'))); ?><br>
              <?php echo e(Form::text('activity[0][measuring_unit]',null, array('class' => 'form-control Item_value_0', 'readonly'=>'readonly'))); ?>

            </td>
            <td>
              <?php echo e(Form::number('activity[0][per_total]',null,array('class' => 'form-control','readonly','placeholder'=>'0','step'=>'any'))); ?>

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
    <div class='col-md-12 well'>
      <div class="col-md-7 pull-left"></div>
      <div class="col-md-5 pull-right">
        <span><b>SUBTOTAL <sup>*</sup> :</b> </span>
        <?php echo e(Form::number('sub_total',null,array('class' => 'form-control','readonly','id'=>'sub_total','step'=>'any', 'required'=>'required'))); ?>

        <br>
        <br>
        <span><b>Discount :</b> </span>
        <?php echo e(Form::number('discount',null,array('class' => 'form-control','id'=>'discount1','step'=>'any'))); ?>

        <span><b>VAT <sup>*</sup> (<select class="selectvatbox1" required="" name="vat_percent"><option value="0">0 %</option><option value="5">5 %</option><option value="15">15 %</option></select>) :</b> </span>
        <?php echo e(Form::number('vat',null,array('class' => 'form-control','id'=>'total_vat','readonly','step'=>'any', 'required'=>'required'))); ?>

        <br>          
        <span><b>GRAND TOTAL <sup>*</sup> :</b> </span>
        <?php echo e(Form::number('grand_total',null,array('class' => 'form-control','id'=>'total_price','readonly','step'=>'any', 'required'=>'required'))); ?>

      </div>
    </div>      
  </div>

  <div class="form-group">
    <div class="col-md-offset-1">
      <div class='col-md-10 well'>
        <span><b>Amount In Word <sup>*</sup> :</b> </span>
        <?php echo e(Form::text('inword',null,array('class' => 'form-control','id'=>'inword', 'required'=>'required'))); ?>

        <br>
        <span><b>Special Instructions :</b> </span>
        <?php echo e(Form::text('special_instruction',null,array('class' => 'form-control','id'=>'special_instruction'))); ?>

      </div>
    </div>  
  </div>
  <div class="col-md-12 multi-column">
     <div class="col-md-4">
      <div class="form-group">
        <label for="accountno" class="control-label">Purchase Person <sup>*</sup> </label>
        <?php echo e(Form::select('purchase_person',array(''=>'Select Person ')+$purchase_persons,null,array('class' => 'form-control', 'required'=>'required'))); ?> 
        <?php echo $errors->first('purchase_person', '<p class="text-danger">:message</p>' ); ?>

      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="account_type" class="control-label">Purchase Status</label>
        <?php echo e(Form::select('status',config('myhelpers.purchase_status'),null,array('class' => 'form-control'))); ?>     
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
<?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
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
      if($item_types){
        foreach ($item_types as $key => $value) {
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-live\resources\views/admin/purchase/create.blade.php ENDPATH**/ ?>