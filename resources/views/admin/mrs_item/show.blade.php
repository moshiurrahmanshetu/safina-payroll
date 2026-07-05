@extends('layouts.admin')
@section('title', 'Mrs Item Details')
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
    <h5 style="text-decoration: underline; margin-bottom: 20px;">MRS Item Details <sup>(MRS Online No. {{$mrs_item->id}})</sup></h5>
    <h3 class="no-print"><a class="btn btn-info pull-right txt_white" onclick="print_this_page();" href="javascript:void(0);">Print</a><br></h3>
  </div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12 multi-column"><hr><br>
    <div class="col-sm-12 col-md-6">
      <h5 style="text-decoration: underline; margin-bottom: 20px;">MRS Details:</h5>
      <table class="table table-striped">
        <tr><th>MRS Online No.</th> <td>{!!$mrs_item->id!!}</td></tr>
        <tr><th>Item Name</th> <td>{!!$mrs_item->name!!}
          @php $combinations=json_decode($mrs_item->combinations, true); @endphp
          @if($combinations!='')
            @foreach($combinations as $key => $values)
              <br><b>{{$key}}</b>: {{$values}}
            @endforeach
          @endif
        </td></tr>
        <tr><th>Purpose Type</th> <td>{{ config('myhelpers.purpose_type')[$mrs_item->requisition->purpose_type] }}</td></tr>
        <tr><th>Purpose Name</th> <td>{{$mrs_item->requisition->purpose->name}}</td></tr>
        <tr><th>Quantity</th> <td>{!!$mrs_item->quantity+0!!} &nbsp; {!!$mrs_item->measuring_unit!!}</td></tr>
        <tr><th>Received Date</th> <td>{{date('d-m-Y',strtotime($mrs_item->received_date))}}</td></tr>
        <tr><th>Received From</th> <td>{!!$users[$mrs_item->user_id]!!}</td></tr>
        <tr><th>Received By</th> <td>{!!$users[$mrs_item->received_by]!!}</td></tr>
        <tr><th>Item Condition</th> <td><strong>{{config('myhelpers.item_condition')[$mrs_item->item_condition]}}</strong></td></tr>
        <tr><th>Warehouse</th> <td>{!!$mrs_item->warehouse->name!!}</td></tr>
        <tr><th>Admin Comments</th> <td>{!!$mrs_item->admin_comments!!}</td></tr>
        <tr><th>Created At</th><td>{{date('d-m-Y',strtotime($mrs_item->created_at))}}</td></tr>
        <tr><th>Updated At</th><td>{{date('d-m-Y',strtotime($mrs_item->updated_at))}}</td></tr>
      </table>
    </div>
      <div class="col-sm-12 col-md-6">
        <h5 style="text-decoration: underline; margin-bottom: 20px;">Requisition Details:</h5>
      <table class="table table-striped">
        <tr><th>Req. Online #</th> <td>{!!$mrs_item->requisition_id!!}</td></tr>
        <tr><th>Item Name</th> <td>{!!$mrs_item->requisition_item->name!!}
          @php $combinations=json_decode($mrs_item->requisition_item->combinations, true); @endphp
          @if($combinations!='')
            @foreach($combinations as $key => $values)
              <br><b>{{$key}}</b>: {{$values}}
            @endforeach
          @endif
        </td></tr>
        <tr><th>Purpose Type</th> <td>{{ config('myhelpers.purpose_type')[$mrs_item->requisition->purpose_type] }}</td></tr>
        <tr><th>Purpose Name</th> <td>{{$mrs_item->requisition->purpose->name}}</td></tr>
        <tr><th>Quantity</th> <td>{!!$mrs_item->requisition_item->given_quantity+0!!} &nbsp; {!!$mrs_item->requisition_item->measuring_unit!!}</td></tr>
        <tr><th>Stock Out Date</th><th>
          @if($mrs_item->requisition_item->stock_out_date)
            {{date('d-m-Y',strtotime($mrs_item->requisition_item->stock_out_date))}}
          @endif
        </th></tr>
        <tr><th>Requisition By</th> <td>{!!$users[$mrs_item->requisition->user_id]!!}</td></tr>
        <tr><th>Given By</th> <td>{!!$mrs_item->requisition->given_by!!}</td></tr>
        <tr><th>Received By</th> <td>{!!$mrs_item->requisition->received_by!!}</td></tr>
        <tr><th>Product Type</th> <td><strong>{{config('myhelpers.product_type')[$mrs_item->requisition_item->product_type]}}</strong></td></tr>
        <tr><th>Description</th> <td>{!!$mrs_item->requisition_item->description!!}</td></tr>
        <tr><th>Created At</th><td>{{date('d-m-Y',strtotime($mrs_item->requisition_item->created_at))}}</td></tr>
        <tr><th>Updated At</th><td>{{date('d-m-Y',strtotime($mrs_item->requisition_item->updated_at))}}</td></tr>
        <tr><th>Show Details</th><td>{!!HTML::decode(link_to_route('admin_requisition_show', '<i class="nav-icon icon-eye"></i>', array($mrs_item->requisition_id)))!!}</td></tr>
      </table>
    </div>
  </div>

</div>

</div>
<br>
@endsection
@section('script')
<script>
  function print_this_page(){
    window.print();
  }
</script>
@endsection