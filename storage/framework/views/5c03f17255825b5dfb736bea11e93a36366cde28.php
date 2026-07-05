<script>  
//Start role delete options
function callModal(selector){
  var options='<?php if(isset($options)){ echo $options; } ?>'; 
  var my_obj = $('#del-form');
  var my_action = my_obj.attr('action');
  var my_id = selector;
  var my_actions = my_action.replace("remove-id", my_id);
  my_obj.attr('action', my_actions);
  $('#selectBox').empty();
  $("#selectBox").append(options);
  $("#selectBox option[value='"+my_id+"']").remove();
}
//End role delete options

//Start general delete options
function callModal2(selector){  
  var my_obj = $('#del-form');
  var my_action = my_obj.attr('action');
  var my_id = selector;
  var my_actions = my_action.replace("remove-id", my_id);
  my_obj.attr('action', my_actions);
}
//End general delete options


function received_products(req_id){
  if(req_id != ''){
    $.ajax({
      type: "get",
      url:"<?php echo e(route('ajax.received_products')); ?>",
      data:{req_id:req_id},
      success: function(data){ 
        if(data==1){
          location.reload(true);
        }
      }
    });
  }    
}

function show_supplier_names(supplier_type){
  $('#supplier_lists').children('option:not(:first)').remove();
  if(supplier_type == ''){
    $('.nullItem').each(function(index, obj){
      this.value = '';
    });
  }else{
    $.ajax({
      type: "get",
      url:"<?php echo e(route('ajax.get_supplier_lists')); ?>",
      data:{supplier_type:supplier_type},
      success: function(data){  
        $.each(data, function(key, value) {
          $('#supplier_lists')
          .append($("<option></option>")
            .attr("value",key)
            .text(value));
        });
      }
    });
  }    
}

//Start onChange show Supllier_info on Purchase 
function show_supllier_info(clientId){    
  if(clientId == ''){
    $('.nullItem').each(function(index, obj){
      this.value = '';
    });
  }else{
    $.ajax({
      type: "get",
      url:"<?php echo e(route('ajax.get_supllier_info')); ?>",
      data:{clientId:clientId},
      success: function(data){
        for(var key in data){
          $("input[name=\'" + key + "\']").val(data[key]);
          $("textarea[name=\'" + key + "\']").val(data[key]);
        }
      }
    });
  }    
}

//Start onChange show item details on purchase 
function show_item_details(clientId, item_index){
  if(clientId == ''){
    $("#attri_"+item_index).html('');
    $('.Item_value_'+item_index).each(function(item_index, obj){
      this.value = '';
    });
  }else{
    $.ajax({
      type: "get",
      url:"<?php echo e(route('ajax.get_item_info')); ?>",
      data:{clientId:clientId},
      success: function(data){
        var additional='';
        $.each(JSON.parse(data.additional), function(key, value) {
          additional+=value.name+': '+value.value+', ';
        });        
        var desc='Category Name: '+data.category.name+', Brand: '+data.brand_name+', Model: '+data.model+', Additional Info: '+additional;
        $("textarea[name=\'activity["+item_index+"][description]\']").val(desc);
        $("select[name=\'activity["+item_index+"][type]\']").val(data.category_id);
        for(var key in data){
        $("input[name=\'activity["+item_index+"][" + key + "]\']").val(data[key]);
        }
        var att_name=''; var op;
        $.each(JSON.parse(data.attributes), function(key, value) {
          var bac='';
          att_name+= '<select required="required" class="form-control combination_'+item_index+'" name="activity1['+item_index+']['+value.name+']">';
          bac+= '<option value="">Select '+value.name+'</option>';
          op=value.values.split('|');
          if(op) {
            for(i = 0; i < op.length; i++) {
              bac+= '<option value="'+op[i]+'">'+op[i]+'</option>';
            }
          }
          att_name+=bac+'</select><br>';
        });
        $("#attri_"+item_index).html(att_name);

      }
    });
  }
}

//Start onChange show item available stock on requisition 
function check_availability(item_id, count, combination, user_id){
  var product_type=$("select[name=\'activity["+count+"][product_type]\']").val();
  if(item_id == ''){
    $("#attri_"+item_id).html('');
  }else{
    $.ajax({
      type: "get",
      url:"<?php echo e(route('ajax.check_availability')); ?>",
      data:{item_id:item_id, combination:combination, product_type:product_type, user_id:user_id},
      success: function(data){
        $("#balance_"+count).html('Available = '+data);
      }
    });
  }
}

function show_user_item_mrs(user_id){
  $('#item_list').children('option:not(:first)').remove();
  $("textarea[name='requisition_details']").val('');
  if(user_id == ''){
  }else{
    $.ajax({
      type: "get",
      url:"<?php echo e(route('ajax.show_user_item_mrs')); ?>",
      data:{user_id:user_id},
      success: function(data){
        $.each(data, function(key, value) {
          $('#item_list')
          .append($("<option></option>")
            .attr("value",key)
            .text(value));
        });
      }
    });
  }    
}

//Start onChange show mrs item details on mrs return 
function show_mrs_item_details(req_item_id){
  if(req_item_id == ''){
    $("textarea[name='requisition_details']").val('');
  }else{
    $.ajax({
      type: "get",
      url:"<?php echo e(route('ajax.get_mrs_item_info')); ?>",
      data:{req_item_id:req_item_id},
      success: function(data){
        var combinations=''; var mrs_items=0;
        var purpose_name=data.requisition.purpose.name;
        $.each(JSON.parse(data.combinations), function(key, value) {
          combinations+=key+': '+value+', ';
        });
        $.each(data.mrs_items, function(key, value) {
          mrs_items=mrs_items+parseFloat(value.quantity);
        });
        var desc='Product Name: '+data.name+', Purpose Name: '+purpose_name+', '+combinations+data.description+', Given Quantity: '+data.given_quantity+', Received Quantity: '+mrs_items+', Measuring Unit: '+data.measuring_unit+', Stock Out Date: '+data.stock_out_date;
        $("textarea[name='requisition_details']").val(desc);
      }
    });
  }
}

// =======form field data calculation start purchase create============= 
var calculate1=function(e)
{
  var subtotal_price=0;
  var subtotal_vat=0;
  var total_price=0;
  var id=-1;
  //alert('hi 5');
  $('.calculate-row').each(function (){
    id=$(this).attr('row-id');
    var unit_price= $("input[name='activity["+id+"][unit_price]']").val();
    var num_of_unit= $("input[ name='activity["+id+"][no_of_unit]']").val();
    var per_total = unit_price? parseFloat(unit_price)*parseFloat(num_of_unit) : 0;
    $("input[ name='activity["+id+"][per_total]']").val(per_total);
    subtotal_price+=per_total;
  });
  var discount= $("input[name='discount']").val();
  var discount = discount? parseFloat(discount) : 0;
  $('#sub_total').val(subtotal_price);
  var vat_percent= $("select[ name='vat_percent']").val();  
  subtotal_price=subtotal_price-discount;        
  subtotal_vat = (subtotal_price*vat_percent)/100;
  $('#total_vat').val(subtotal_vat);
  total_price = subtotal_price + subtotal_vat;
  $('#total_price').val(total_price);
  // numeric to string inWord start
  var a = ['','One ','Two ','Three ','Four ', 'Five ','Six ','Seven ','Eight ','Nine ','Ten ','Eleven ','Twelve ','Thirteen ','Fourteen ','Fifteen ','Sixteen ','Seventeen ','Eighteen ','Nineteen '];
  var b = ['', '', 'Twenty','Thirty','Forty','Fifty', 'Sixty','Seventy','Eighty','Ninety'];
  var num = Math.round(total_price);
  if ((num = num.toString()).length > 9) return 'overflow';
  n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
  if (!n) return; var str = '';
  str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'Crore ' : '';
  str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'Lac ' : '';
  str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'Thousand ' : '';
  str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'Hundred ' : '';
  str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'Only ' : '';
  $('#inword').val(str);  
  // numeric to string inWord end
}
$('body').on('keyup click','.calculate_no_of_unit1',calculate1);
$('body').on('keyup click','.calculate_unit_price1',calculate1);
$('body').on('keyup click','#discount1',calculate1);
$('body').on('change','.selectvatbox1',calculate1);
// =======form field data calculation end purchase create ============= 

//Start onChange show show_type_wise_item_list 
function show_type_wise_item_list(type_id, item_index){
  $("select[name=\'activity["+item_index+"][item_name]\']").children('option:not(:first)').remove();
  $('.Item_value_'+item_index).each(function(item_index, obj){
      this.value = '';
    });
  $("#attri_"+item_index).html('');
  $.ajax({
    type: "get",
    url:"<?php echo e(route('ajax.show_type_wise_item_list')); ?>",
    data:{type_id:type_id},
    success: function(data){
      $.each(data, function(key, value) {
        $("select[name=\'activity["+item_index+"][item_name]\']")
        .append($("<option></option>")
          .attr("value",key)
          .text(value));
      });
    }
  });
}
//End onChange show show_type_wise_item_list

//Start onChange show show_purpose_names 
function show_purpose_names(type_id){
  $("select[name=\'purpose_id\']").children('option:not(:first)').remove();
  if(type_id == ''){
    alert('Please Select a Purpose Type');
  }else{
  $.ajax({
    type: "get",
    url:"<?php echo e(route('ajax.show_purpose_names')); ?>",
    data:{type_id:type_id},
    success: function(data){
      $.each(data, function(key, value) {
        $("select[name=\'purpose_id\']")
        .append($("<option></option>")
          .attr("value",key)
          .text(value));
      });
    }
  });
  }
}
//End onChange show show_purpose_names
</script><?php /**PATH C:\xampp\htdocs\safina-live\resources\views/ajaxs/php_script.blade.php ENDPATH**/ ?>