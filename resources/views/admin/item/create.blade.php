@extends('layouts.admin')
@section('title', 'Product Create')
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
<h3 class="page-header">Product Create {{link_to_route('item.index','Product List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
 {{ Form::model(Request::old(),array('route' => array('item.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
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
          {{Form::number('low_stock',0, array('class' => 'form-control', 'placeholder'=>'Quantity','step'=>'any'))}}
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
        </div>
        <div class="col-md-5 preview-div">
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
      <div class="col-md-4">
        <div class="form-group">
          <span class="btn-success add-attributes" onclick="AddiFunction()">
          <i class="fa fa-plus" aria-hidden="true"></i> Add Additional Field</span>
        </div>
      </div>
      <div class="col-md-12" id="additional">
        <div class=""><label for=""><h3>Addition Fields:</h3></label></div>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="40%" class="text-center"> Name </th>
              <th width="50%" class="text-center"> Values</th>
              <th width="10%" class="text-center"> +/- </th>
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
    </div>
    <div class="col-md-12">
      <div class="form-group">
        <div class="col-md-4">
          <span class="btn-success add-attributes" onclick="myFunction()">
            <i class="fa fa-plus" aria-hidden="true"></i> Add Attributes</span>
        </div>
      </div>
    </div>    
    <div class="col-md-12 multi-column" id="panel">
      <div class="col-md-7">
        <div class=""><label for=""><h3>Attributes:</h3></label></div>
        <table class="table table-bordered table-hover" id="salescount">
          <thead>
            <tr>
              <th width="40%" class="text-center"> Name </th>
              <th width="50%" class="text-center"> Values <sub>(Seperate by '|')</sub></th>     
              <th width="10%" class="text-center"> +/- </th>
            </tr>
          </thead>
          <tbody>
            <tr class="calculate-row" id="0_info" row-id='0'>
              <td>
                {{Form::text('activity[0][name]',null,array('class' => 'form-control', 'placeholder'=>'Attribute Name'))}}
              </td>
              <td>
                {{Form::text('activity[0][values]',null,array('class' => 'form-control', 'placeholder'=>'Attributes Values'))}}
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
      <div class="col-md-5">
        <!-- <div class=""><label for=""><h3>Combinations:</h3></label></div>
        <table class="table table-bordered table-hover">
          <span class="btn-success" onclick="make_combination();">
            <i class="fa fa-plus" aria-hidden="true"></i> Add Combinations
          </span>
          <thead>
            <tr class="row_th"></tr>
          </thead>
          <tbody>
            <tr class="calculate-row1" id="0_info1" row-id='0'></tr>
          </tbody>
        </table> -->
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">
          <button type="submit" class="btn btn-primary">
            Create Product
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
        row_th+='<th class="text-center"> '+name+' </th>';
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
        '<th width="20%" class="text-center"> Sale-Price</th>'+
        '<th width="10%" class="text-center"> +/- </th>';
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
      '           <button type="button" onclick="remove(\''+id2+'_info1\')" class="btn btn-danger btn-sm">'+
      '            <i class="fa fa-minus" aria-hidden="true"></i>'+
      '           </button>'+
      '           </td>'+
      '         </tr>';
       $('#0_info1').after(myvar);
     }
    function myFunction() {
      document.getElementById("panel").style.display = "block";
    }
    function AddiFunction(){
      document.getElementById("additional").style.display="block";
    }
    var addMore = (function () 
    {   
      var id = 1; var rawid=0;
      return function (previous_id) {
        $('.calculate-row').each(function (){
          rawid=$(this).attr('row-id'); rawid=parseInt(rawid);
          if(rawid>=id){ 
            id=rawid+1; 
          }
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
  var addMore3 = (function () 
  {   
    var id = 1; var rawid=0;
    return function (previous_id) {
      $('.calculate-row3').each(function (){
        rawid=$(this).attr('row-id'); rawid=parseInt(rawid);
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
  function removee(id){
      $('#'+id).remove();
  }

</script>
@endsection
