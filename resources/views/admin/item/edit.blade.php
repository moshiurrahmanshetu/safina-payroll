@extends('layouts.admin')
@section('title', 'Update Product')
@section('content')
@section('css')
<style>
  #panel {
    display: none;
  }
  #additional {
    display: none;
  }
  .add-attributes{
    padding: 6px;
    cursor: pointer;
  }
</style>
@endsection
<h3 class="page-header">Update Product  {{link_to_route('item.index','View Product List',null,array('class'=>'btn btn-success pull-right'))}} </h3>
{{ Form::model($items,array('route' => array('item.update', $items->id),'enctype'=>'multipart/form-data', 'class'=>'form-horizontal', 'method' => 'PUT')) }} 
  <div class="row">
    <div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Item Name <sup>*</sup></label>
            {{Form::text('name',null,array('class' => 'form-control', 'required'=>'required'))}}
            {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}    
        </div>
      </div>              
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Brand Name</label>
           {{Form::text('brand_name',null,array('class' => 'form-control'))}}
           {!! $errors->first('brand_name', '<p class="text-danger">:message</p>' ) !!}   
        </div>
      </div>
       <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Model</label>
            {{Form::text('model',null,array('class' => 'form-control'))}}
            {!! $errors->first('model', '<p class="text-danger">:message</p>' ) !!}    
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Product Category <sup>*</sup></label>
            {{Form::select('category_id',$categories,null,array('class' => 'form-control', 'required'=>'required'))}}
            {!! $errors->first('category_id', '<p class="text-danger">:message</p>' ) !!}    
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Measuring Unit <sup>*</sup></label>
            {{Form::select('measuring_unit',config('myhelpers.measuring_unit'),null,array('class' => 'form-control', 'required'=>'required'))}}
            {!! $errors->first('measuring_unit', '<p class="text-danger">:message</p>' ) !!}    
        </div>
      </div>
       <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Low Stock Reminder<sub>Min Value</sub></label>
          {{Form::number('low_stock',null, array('class' => 'form-control', 'placeholder'=>'Quantity','step'=>'any'))}}
          {!! $errors->first('low_stock', '<p class="text-danger">:message</p>' ) !!}    
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-5">
        <div class="col-md-7 form-group">
          <label class="control-label">Image of Item <sub>( 150X150 px thumb image)</sub></label>
            {{Form::file('item_img',array('class' => 'form-control', 'onChange'=>'readURL(this)'))}}  
            {!! $errors->first('item_img', '<p class="text-danger">:message</p>' ) !!} 
            <input type="hidden" name="old_image" value="{{$items->item_img}}">
        </div>
        <div class="col-md-5 preview-div">
          @if($items->item_img)
            <img src="{{asset('storage/app/admin/item/'.$items->item_img)}}" alt="" width="50px" height="50px">
          @endif
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          <label class="control-label">Remarks</label>
            {{Form::textarea('remarks',null,array('class' => 'form-control', 'rows'=>3))}}
            {!! $errors->first('remarks', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label class="control-label">Status <sup>*</sup></label>
            {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}    
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      @if(count($activity3)>0)
      <div class="col-md-12">
        <div class=""><label for=""><h3>Additional Fields:</h3></label></div>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="30%" class="text-center"> Name</th>
              <th width="30%" class="text-center"> Value</th>        
              <th width="15%" class="text-center"> Add/Remove </th>
            </tr>
          </thead>
          <tbody>
            @php $count = 0; @endphp
            @foreach($activity3 as $key3 => $value3)
              <tr class="calculate-row3" id="{{$count}}_info3" row-id='{{$count}}'>
                <td>
                  {{Form::text('activity3['.$count.'][name]',$value3['name'],array('class' => 'form-control', 'placeholder'=>'Name'))}}
                </td>
                <td>
                  {{Form::text('activity3['.$count.'][value]',$value3['value'],array('class' => 'form-control', 'placeholder'=>'Value'))}}
                </td>
                <td>
                @if($loop->last)
                  <button type="button" onclick="addMore3('{{$count}}_info3')" class="btn btn-success btn-sm">
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
    @else
    <div class="form-group">
      <span class="btn-success add-attributes" onclick="AddiFunction()">
      <i class="fa fa-plus" aria-hidden="true"></i> Add Additional Field</span>
     </div>
      <div class="col-md-12" id="additional">
        <div class=""><label for=""><h3>Addition Fields:</h3></label></div>
          <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="40%" class="text-center"> Name </th>
              <th width="40%" class="text-center"> Values</th>     
              <th width="15%" class="text-center"> Add/Remove </th>
            </tr>
          </thead>
          <tbody>
            <tr class="calculate-row3" id="0_info3" row-id='0'>
              <td>
                {{Form::text('activity3[0][name]',null,array('class' => 'form-control', 'placeholder'=>'Name'))}}
              </td>
              <td>
                {{Form::text('activity3[0][value]',null,array('class' => 'form-control', 'placeholder'=>'Value'))}}
              </td>
              <td>
                <button type="button"  onclick="addMore3('0_info3')" class="btn btn-success btn-sm">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    @endif
  </div>
    @if(count($activity)>0)
    <div class="col-md-12 multi-column">
      <div class="col-md-7">
        <div class=""><label for=""><h3>Attributes:</h3></label></div>
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th width="20%" class="text-center">
              Attributes Name
            </th>
            <th width="30%" class="text-center">
              Attributes Values
            </th>
            <th width="10%" class="text-center">
              Add/Remove
            </th>
          </tr>
        </thead>
        <tbody>
          @php $count = 0; $row_th='';  $id = 1; $id2 = 1; $rawid=0; $op; $att=array(); $att_val=array(); @endphp
          @foreach($activity as $key=>$value)
            <tr class="calculate-row" id="{{$count}}_info" row-id='{{$count}}'>
              <td>
                {{Form::text('activity['.$count.'][name]',$value['name'],array('class' => 'form-control', 'placeholder'=>'Attributes Name'))}}
              </td>
              <td>
                {{Form::text('activity['.$count.'][values]',$value['values'],array('class' => 'form-control', 'placeholder'=>'Attributes Values'))}}
              </td>
              <td>
                @if($loop->last)
                  <button type="button" onclick="addMore('{{$count}}_info')" class="btn btn-success btn-sm">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                  </button>
                @endif
              </td>
            </tr>
            @php $count++; 
              $name=$value['name']; $att[$value['name']]=$value['name'];
              $row_th.='<th width="25%" class="text-center"> '.$name.' </th>';
              $values=$value['values'];
              $op=explode('|',$values); $att_op=array();
              foreach($op as $item){
                $att_op[$item]=$item;
              }
              $att_val[$value['name']]=$att_op;
            @endphp
          @endforeach
          @php 
           $row_th.='<th width="20%" class="text-center"> Regular Price</th><th width="20%" class="text-center"> Sale Price</th><th width="10%" class="text-center"> Add/Remove </th>';
          @endphp
        </tbody>
      </table>
      </div>
      <div class="col-md-5">
        <!-- <div class=""><label for=""><h3>Combinations:</h3></label></div><span class="btn-success" id="show" onclick="make_combination();"> <i class="fa fa-plus" aria-hidden="true"></i> Re Combination</span>
        <table class="table table-bordered table-hover" id="hide">
          <thead>
            <tr> 
              {!!$row_th!!}
            </tr>
          </thead>
          <tbody>
            @php $count = 0; @endphp
            @foreach($activity1 as $key=>$value)
              <tr class="calculate-row1" id="{{$count}}_info1" row-id='{{$count}}'>
                @foreach($att as $key1=>$value1)
                  <td>
                    {{Form::select('activity1['.$count.']['.$key1.']',array(''=>'Select')+$att_val[$key1],$value[$key1],array('class' => 'form-control'))}}
                  </td>
                @endforeach
                <td>
                  {{Form::number('activity1['.$count.'][regular_price]',$value['regular_price'],array('class' => 'form-control'))}}
                </td>
                <td>
                  {{Form::number('activity1['.$count.'][sale_price]',$value['sale_price'],array('class' => 'form-control'))}}
                </td>
                <td>
                @if($loop->last)
                  <button type="button"  onclick="addMore1('{{$count}}_info1')" class="btn btn-success btn-sm">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                  </button>
                @endif
                </td>
              </tr>
              @php $count++;@endphp
            @endforeach
          </tbody>
        </table>
        <table class="table table-bordered table-hover" id="data">
          <thead>
            <tr class="row_th">
            </tr>
          </thead>
          <tbody>
            <tr class="calculate-row1" id="0_info1" row-id='0'></tr>
          </tbody>
        </table> -->
      </div>
    </div>
    @else 
    <div class="col-md-12">
      <div class="form-group">
        <div class="col-md-4">
          <span class="btn-success add-attributes" onclick="myFunction()">
            <i class="fa fa-plus" aria-hidden="true"></i> Add Attributes</span>
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column" id="panel">
      <div class="col-md-5">
        <div class=""><label for=""><h3>Attributes:</h3></label></div>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="40%" class="text-center"> Name </th>
              <th width="40%" class="text-center"> Values</th>     
              <th width="20%" class="text-center"> Add/Remove </th>
            </tr>
          </thead>
          <tbody>
            <tr class="calculate-row" id="0_info" row-id='0'>
              <td>
                {{Form::text('activity[0][name]',null,array('class' => 'form-control', 'placeholder'=>'Attribute Name'))}}
              </td>
              <td>
                {{Form::text('activity[0][values]',null,array('class' => 'form-control', 'placeholder'=>'Attributes Value'))}}
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
      <div class="col-md-7">
        <div class=""><label for=""><h3>Combinations:</h3></label></div>
        <span class="btn-success" onclick="make_combination();"> <i class="fa fa-plus" aria-hidden="true"></i> Add Combinations</span>
      <table class="table table-bordered table-hover">
        <thead>
          <tr class="row_th"></tr>
        </thead>
        <tbody>
        <tr class="calculate-row1" id="0_info1" row-id='0'></tr>
        </tbody>
      </table>
      </div>
    </div>
    @endif
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">
          <button type="submit" class="btn btn-primary">
            Update Product
          </button>
        </div>
      </div>
    </div>
</div>
{{ Form::close() }}
@endsection
@section('script')
<script>

  function make_combination(){
    var id = 1; var id2 = 1; var rawid=0; var row_th=''; var row_td=''; var op; 
     $('.calculate-row1').each(function (){
        rawid2=$(this).attr('row-id'); rawid2=parseInt(rawid2);
        if(rawid2>=id2){ id2=rawid2+1; }
      });
      $('.calculate-row').each(function (){
          rawid=$(this).attr('row-id'); rawid=parseInt(rawid);
          var name=$("input[name=\'activity["+rawid+"][name]\']").val();
          var values=$("input[name=\'activity["+rawid+"][values]\']").val();
          row_th+='<th width="25%" class="text-center"> '+name+' </th>';
          op=values.split('|'); var bac='';
          if(op){
            for(i = 0; i < op.length; i++) {
              bac+= '<option value="'+op[i]+'">'+op[i]+'</option>';
            }
          }
          row_td+='<td> <select class="form-control" name="activity1['+id2+']['+name+']">'+bac+'</select> </td>';
          if(rawid>=id){ 
            id=rawid+1; 
          }
      });
    row_th+='<th width="20%" class="text-center"> Regular Price</th>'+
        '<th width="20%" class="text-center"> Sale Price</th>'+
        '<th width="10%" class="text-center"> Add/Remove </th>';
      $(".row_th").html(row_th);

      var myvar = '<tr id="'+id2+'_info1" class="calculate-row1" row-id="'+id2+'">'+
      '           '+row_td+
      '           <td>'+
      '           <input type="number" name="activity1['+id2+'][regular_price]" class="form-control" step="any"/>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity1['+id2+'][sale_price]" class="form-control" step="any"/>'+
      '           </td>'+
      '           <td>'+
      '           '+
      '           <button type="button" onclick="remove1(\''+id2+'_info1\')" class="btn btn-danger btn-sm">'+
      '            <i class="fa fa-minus" aria-hidden="true"></i>'+
      '           </button>'+
      '           </td>'+
      '         </tr>';
       $('#0_info1').after(myvar);
       var remove = document.getElementById("hide");
        remove.remove();
     }
    var addMore = (function () 
    {   
      var id = 1; var rawid=0;
      return function (previous_id) {
        $('.calculate-row').each(function (){
          rawid=$(this).attr('row-id'); rawid=parseInt(rawid);
          if(rawid>=id){ id=rawid+1; }
      });
        var myvar = '<tr id="'+id+'_info" class="calculate-row" row-id="'+id+'">'+
        '           <td>'+
        '           <input type="text" name="activity['+id+'][name]" placeholder="Attributes Name" class="form-control"/>'+
        '           </td>'+
        '           <td>'+
        '           <input type="text" name="activity['+id+'][values]" placeholder="Attributes Values" class="form-control" step="any"/>'+
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
    function re_combination(){
     var remove = document.getElementById("hide");
     remove.remove();
  }
  $("#show").click(function(){
    $("#data").show();
  });
  function myFunction() {
    document.getElementById("panel").style.display = "block";
  }
  function AddiFunction(){
    document.getElementById("additional").style.display="block";
  }
  var addMore1 = (function () 
  {   
    var id = 1; var rawid=0;
    return function (previous_id) {
      $('.calculate-row1').each(function (){
        rawid=$(this).attr('row-id'); rawid=parseInt(rawid);
        if(rawid>=id){ id=rawid+1; }
      });
      var myvar = '<tr id="'+id+'_info" class="calculate-row1" row-id="'+id+'">';
      <?php $myver='';
      if (count($activity)>0) {
        foreach ($att as $key => $value) { 
          $bac='<option value="">Select</option>';
          foreach ($att_val[$key] as $key1 => $value1) {
            $bac.='<option value="'.$value1.'">'.$value1.'</option>';
          }
          $myver.='<td> <select class="form-control" name="activity1['.$count.']['.$value.']">'.$bac.'</select> </td>';
        }}
      ?>
      myvar+='<?php echo $myver; ?>';
      myvar +='<td>'+
      '           <input type="number" name="activity1['+id+'][regular_price]" class="form-control" step="any"/>'+
      '           </td>'+
      '           <td>'+
      '           <input type="number" name="activity1['+id+'][sale_price]" class="form-control" step="any"/>'+
      '           </td>'+
      '           <td>'+
      '           <button type="button" onclick="addMore1(\''+id+'_info\')" class="btn btn-success btn-sm abc">'+
      '           <i class="fa fa-plus" aria-hidden="true"></i>'+
      '           </button>'+
      '           <button type="button" onclick="remove1(\''+id+'_info\')" class="btn btn-danger btn-sm">'+
      '            <i class="fa fa-minus" aria-hidden="true"></i>'+
      '           </button>'+
      '           </td>'+
      '         </tr>';

      $('#'+previous_id).after(myvar);
    };
  })();
  function remove1(id) {
    $('#'+id).remove();
  }
  var addMore3 = (function () {   
    var id = 1; var rawid=0;
    return function (previous_id) {
      $('.calculate-row3').each(function (){
        rawid=$(this).attr('row-id'); 
        rawid=parseInt(rawid);
        if(rawid>=id){ id=rawid+1; }
      });
      var myvar = '<tr id="'+id+'_info3" class="calculate-row3" row-id="'+id+'">'+
      '           <td>'+
      '           <input type="text" name="activity3['+id+'][name]" placeholder="Name" class="form-control"/>'+
      '           </td>'+
      '           <td>'+
      '           <input type="text" name="activity3['+id+'][value]" placeholder="Values" class="form-control" step="any"/>'+
      '           </td>'+
      '           <td>'+
      '           <button type="button" onclick="addMore3(\''+id+'_info3\')" class="btn btn-success btn-sm abc">'+
      '           <i class="fa fa-plus" aria-hidden="true"></i>'+
      '           </button>'+
      '           <button type="button" onclick="remove(\''+id+'_info3\')" class="btn btn-danger btn-sm">'+
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
