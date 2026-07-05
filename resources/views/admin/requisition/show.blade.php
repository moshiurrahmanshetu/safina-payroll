@extends('layouts.admin')
@section('title', 'Requisition Details')
@section('css')
<style type="text/css">
  table.table, table.table tr th, table.table tr td{
    border: 1px solid #000;
  }
  .print-area{
    border: 2px solid #000;
    padding: 5px;
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
          {{HTML::image('storage/app/admin/users/'.$user->signature, null, array('width'=>'70', 'class'=>'img-responsive')) }}<br>{!!date('d-m-Y', strtotime($requisitions->created_at))!!}
          </p>
          <br><b>Requisitioner</b><br>(Sign with Seal) <br>{!!$user->name!!} </h5>
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
@if($requisitions->status==3 && $requisitions->received_status!='received')
<hr class="no-print"><br>
  <div class="row no-print">
    <div class="col-md-12 multi-column">
      <div class="col-md-12">
        <div class="form-group text-center">
          <label class="control-label">Did you Received this Products? 
            <button class="btn-success" onclick="received_products({{$requisitions->id}})">Yes</button>
          </label>
        </div>
      </div>
    </div>
  </div>
@endif

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

@endsection
@section('script')
<script>
  function print_this_page(){
    window.print();
  }
</script>
@endsection