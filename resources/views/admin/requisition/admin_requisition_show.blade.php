@extends('layouts.admin')
@section('title', 'Admin Requisition Show')
@section('css')
<style type="text/css">
  table.table, table.table tr th, table.table tr td{
    border: 1px solid #000;
  }
  .print-area, .block-box{
    border: 2px solid #000;
    padding: 5px;
  }
  .hidden{
    display: none;
  }
  @media print {
    .no-print, .app-footer, .alert{
      display: none;
    }
    .content-body{
      padding-top:140px !important;
      position: relative;
    }
  }
</style>
@endsection
@section('content')
<div class="print-area">
<div class="row page-header">
  <div class="col-sm-12 col-md-12 text-center">
    <h3 class="txt_green"> Safina Park & Resort Ltd., Godagari, Rajshahi</h3>
    <h5 style="text-decoration: underline; margin-bottom: 20px;">Requisition for Stationery Articles <sup>(Req. Online No. {{$requisitions->id}})</sup></h5>
    <h3 class="no-print"><a class="btn btn-info pull-right txt_white" onclick="print_this_page();" href="javascript:void(0);">Print</a><br></h3>
  </div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12 multi-column">
    <table class="table table-striped">
      <thead>
        <tr> 
          <th>SL No</th>
          <th>Name of Articles</th>
          <th width="30%">Description</th>
          <th>Req. Quantity</th>
          <th>Given Quantity</th>
        </tr>
      </thead>
      <tbody>
        @php $i=1; @endphp
        @foreach($requisitions->requisition_items as $data)
        @php $combinations=json_decode($data->combinations, true); @endphp
        <tr>
          <td class="text-center">{{$i}}</td>
          <td>{{$data->name}} @if($data->returnable==1) (R) @endif
            @if($combinations!='')
              @foreach($combinations as $key => $values)
                <br> <b>{{$key}}</b>: {{$values}} 
              @endforeach
            @endif
          </td>
          <td>{{$data->description}}</td>
          <td class="text-center">{{$data->req_quantity+0}} {{$data->measuring_unit}}</td>
          <td class="text-center">{{$data->given_quantity+0}}</td>
        </tr>
        @php $i=$i+1; @endphp
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12 multi-column">
    <h5> &nbsp; <b>Purpose Type: </b> {!!config('myhelpers.purpose_type')[$requisitions->purpose_type]!!},  &nbsp; <b>Purpose Name: </b> {!!$requisitions->purpose->name!!} </h5>
  </div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12 multi-column"><br><br><br><br>
    <div class="col-md-4">
      <div class="form-group">
        <h5><b>Received By: </b>  {{$requisitions->received_by}}</h5>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <h5 class="text-center">
          <p style="height:50px;">
          {{HTML::image('storage/app/admin/users/'.$requisitions->user->signature, null, array('width'=>'70', 'class'=>'img-responsive')) }}<br>{!!date('d-m-Y', strtotime($requisitions->created_at))!!}
          </p>
          <br><b>Requisitioner</b><br>(Sign with Seal) <br>{!!$requisitions->user->name!!} </h5>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <h5 class="text-center"><p style="height:50px;">
          @if($requisitions->counter_sign_status==1)
            {{HTML::image('storage/app/admin/users/'.$requisitions->supervisor->signature, null, array('width'=>'70', 'class'=>'img-responsive')) }}<br>
            @if($requisitions->counter_sign_date)
              {!!date('d-m-Y', strtotime($requisitions->counter_sign_date))!!}
            @endif
          @else
            <strong class="btn-{{ config('myhelpers.status_color')[$requisitions->counter_sign_status] }}">{{config('myhelpers.counter_sign_status')[$requisitions->counter_sign_status]}}</strong>
            <br>
            @if($requisitions->counter_sign_date)
              {!!date('d-m-Y', strtotime($requisitions->counter_sign_date))!!}
            @endif
          @endif
          </p>
          <br><b>Counteration Officer</b><br>(Sign with Seal) <br>{!!$requisitions->supervisor->name!!} </h5>
      </div>
    </div>    
  </div>
</div>
</div>
<br><br>
<div class="row no-print">
  <div class="col-sm-12 col-md-12 multi-column"><hr><br>
    <div class="col-sm-12 col-md-6">
      <table class="table table-striped">
        <tr><th>Counter Sign Status</th><td><strong class="btn-{{ config('myhelpers.status_color')[$requisitions->counter_sign_status] }}">{{config('myhelpers.counter_sign_status')[$requisitions->counter_sign_status]}}</strong></td></tr>
        <tr><th>Requisition Status</th><td><strong class="btn-{{ config('myhelpers.status_color')[$requisitions->status] }}">{{config('myhelpers.requisition_status')[$requisitions->status]}}</strong></td></tr>
        @if($requisitions->received_status=='received')
          <tr><th>Received Status</th><td><strong class="btn-success">Received &#10003;</strong></td></tr>
        @endif
        <tr><th>Requisitioner Comments</th> <td>{!!$requisitions->requisitioner_comments!!}</td></tr>
        <tr><th>Supervisor Comments</th><td>{{$requisitions->supervisor_comments}}</td></tr>
        <tr><th>Admin Comments</th> <td>{!!$requisitions->admin_comments!!}</td></tr>
      </table>
    </div>
      <div class="col-sm-12 col-md-6"> 
      <table class="table table-striped">
        <tr><th>Stock Out Date</th><th>
          @if($requisitions->stock_out_date)
            {{date('d-m-Y',strtotime($requisitions->stock_out_date))}}
          @endif
        </th></tr>
        <tr><th>Requested By</th><td>{!!$users[$requisitions->created_by]!!}</td></tr>
        <tr><th>Last Updated By</th><td>{!!$users[$requisitions->updated_by]!!}</td></tr> 
        <tr><th>Requested At</th><td>{{date('d-m-Y',strtotime($requisitions->created_at))}}</td></tr>
        <tr><th>Updated At</th><td>{{date('d-m-Y',strtotime($requisitions->updated_at))}}</td></tr>
        <tr><th>Given By</th><td> {{$requisitions->given_by}}</td></tr>
      </table>
    </div>
  </div>
</div>
@if(($requisitions->status<3)&&($requisitions->counter_sign_status==1))
<hr class="no-print"><br>
<div class="no-print block-box"><h3 class="txt_green"> Update Requisition</h3>
  <hr>
{{ Form::model($requisitions,array('route' => array('admin_requisition_update', $requisitions->id), 'class'=>'form-horizontal', 'method' => 'PUT')) }} 
  <div class="row">
    <div class="col-md-12 multi-column">
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label">Status <sup>*</sup></label>
          {{Form::select('status',config('myhelpers.requisition_status'),null,array('class' => 'form-control', 'id'=>'status_change'))}}
          {!!$errors->first('status', '<p class="text-danger">:message</p>')!!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Admin Comments/Instructions</label>
          {{Form::textarea('admin_comments',null,array('class' => 'form-control', 'rows'=>'4', 'id'=>'admin_comments'))}}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group" id="auto-text-feed">
          <label class="control-label">Choose Predefined Text for Admin Comments</label><br>
          <span class="btn-info">বর্তমানে স্টোরে আপনার চাহিত পণ্যটি নাই</span>
          <span class="btn-info">স্টোক হওয়ার পর ব্যবস্থা গ্রহণ করা হবে</span>
          <span class="btn-info">স্টোরে আপনার চাহিত পণ্যটি নাই</span>
          <span class="btn-info">চাহিত পণ্যটি নাই</span>
          <span class="btn-info">ব্যবস্থা গ্রহণ করা হবে</span>
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Given By</label>
          {{Form::text('given_by',$user->name, array('class' => 'form-control','readonly'))}}
        </div>
      </div> 
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Received By</label>
          {{Form::text('received_by',null, array('class' => 'form-control'))}}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label">Stock Out Date <sup class="hidden">*</sup></label>
          {{Form::text('stock_out_date',null, array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'id'=>'date_required'))}}
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column"> 
      <div>
      <div class=""><label for=""><h3>Item Details:</h3></label></div>
      <table class="table table-bordered table-hover" id="salescount">
        <thead>
          <tr>
            <th width="4%">SI #</th>
            <th width="23%" class="text-center">
              Product Name
            </th>
            <th width="10%" class="text-center">
              Type
            </th>
            <th width="8%" class="text-center">
              Is Returnable?
            </th>
            <th width="30%" class="text-center">
              Description
            </th>
            <th width="15%" class="text-center">
              Req. QTY
            </th>
            <th width="10%" class="text-center">
              Given QTY
            </th>
          </tr>
        </thead>
        <tbody>
          @php $count = 0; @endphp
          @foreach($requisitions->requisition_items as $key=>$value)
          <tr class="calculate-row" id="{{$count}}_info" row-id='{{$count}}'>
            @php $combinations=json_decode($value->combinations, true); @endphp
            <td>{{$count+1}}</td>
            <td>{{$value->name}}
            @if($combinations!='')
              @foreach($combinations as $key2 => $values2)
                <br> <b>{{$key2}}</b>: {{$values2}} 
              @endforeach
            @endif
            {{Form::hidden('activity['.$count.'][id]',$value->id)}}
            </td>
            <td>{{Form::select('activity['.$count.'][product_type]',config('myhelpers.product_type'), $value->product_type, array('class' => 'form-control'))}}</td>
            <td>{{Form::select('activity['.$count.'][returnable]',array('0'=>'No','1'=>'Yes'), $value->returnable, array('class' => 'form-control'))}}</td>
            <td>
            {{Form::textarea('activity['.$count.'][description]',$value->description,array('class' => 'form-control', 'placeholder'=>'Description', 'rows'=>4))}}
            </td>
            <td class="text-center">{{$value->req_quantity+0}} {{$value->measuring_unit}}
              <br><hr><a href="javascript:void(0)" id="balance_{{$count}}" class="no-print btn-info" onclick="check_availability({{$value->item_id}},{{$count}},'{{$value->combinations}}', {{$requisitions->user_id}})">check availability</a></td>
            <td>
              {{Form::number('activity['.$count.'][given_quantity]',$value->given_quantity,array('class' => 'form-control', 'placeholder'=>'No of unit','step'=>'any'))}}
            </td>
          </tr>
          @php $count++; @endphp
          @endforeach
        </tbody>
      </table>
      </div>
    </div>
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
</div><br>
@endif
@endsection
@section('script')
<script>
  function print_this_page(){
    window.print();
  }
  $("#auto-text-feed span").click(function(){
    var auto_text=$(this).text();
    var old_text=$('#admin_comments').val();
    if(old_text){
      $('#admin_comments').val(old_text+' '+auto_text);
    }else{
      $('#admin_comments').val(auto_text);
    }
  });
  $("#status_change").change(function(){
    var get_status=$(this).val();
    if(get_status==3){
      $('#date_required').attr('required',true);
      $('.hidden').css('display','inline');
    }else{
      $('#date_required').attr('required',false);
      $('.hidden').css('display','none');
    }
  });
</script>
@endsection