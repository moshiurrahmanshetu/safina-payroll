@extends('layouts.admin')
@section('title', 'Purchase Transaction')
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
<h3 class="page-header">Purchase Transaction  <a class="btn btn-info txt_white no-print" onclick="print_this_page();" href="javascript:void(0);">Print</a>{{link_to_route('purchase_transaction.index','Transaction List',[],array('class'=>'btn btn-success pull-right no-print'))}}</h3>
  <div class="row">
    <div class="col-md-12 multi-column">
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label"><h5><b>Purchase Info:</b></h5></label><br>
          <b>Purchase Date:</b> {{date('d-m-Y',strtotime($purchase_transactions->purchase->purchase_date))}}<br>
          <b>Purchase Price:</b> {{$purchase_transactions->purchase->grand_total+0}}<br>
          <b>Purchase Status:</b> {{config('myhelpers.purchase_status')[$purchase_transactions->purchase->status]}}
        </div><hr><br>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label"><h5><b>Supplier Info:</b></h5></label><br>
          <b>Name:</b> {{$purchase_transactions->supplier->contact_name}} ({{$purchase_transactions->supplier->company_name}})<br>
          <b>Mobile#:</b> {{$purchase_transactions->supplier->mobile}}<br>
          <b>Email:</b> {{$purchase_transactions->supplier->email}}
        </div><hr><br>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="control-label"><h5><b>Transaction Info:</b></h5></label><br>
          @foreach($transactions as $item)
          <b>Total Paid:</b> {{$item->total+0}}<br>
          <b>Total Due:</b> {{$purchase_transactions->purchase->grand_total-$item->total}}<br>
          <b>Last Paid:</b> {{$transaction_last->amount+0}} / {{date('d-m-Y',strtotime($transaction_last->payment_date))}}
          @endforeach
        </div><hr><br>
      </div>
    </div> 
    <h3 class="page-header" style="padding-left:30px">Transaction Details</h3>
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <table class="table table-striped">
          <tr><th>Payment Type</th><td>{{config('myhelpers.payment_type')[$purchase_transactions->payment_type]}}</td></tr>
          <tr><th>Transaction Person</th><td>{{$transaction_persons[$purchase_transactions->given_by]}}</td></tr>
          <tr><th>Received By</th><td>{{$purchase_transactions->received_by}}</td></tr>
          <tr><th>Money Rceipt No</th><td>{{$purchase_transactions->money_rceipt_no}}</td></tr>
        </table>
      </div>
      <div class="col-md-6">
        <table class="table table-striped">
          <tr><th>Amount</th><td>{{$purchase_transactions->amount+0}}</td></tr>
          <tr><th>Transaction Date</th><td>{{date('d-m-Y',strtotime($purchase_transactions->payment_date))}}</td></tr>
          <tr><th>Photo</th>
            <td>
            @if($purchase_transactions->attachment_copy)
              <img src="{{asset('storage/app/admin/transactions/'.$purchase_transactions->attachment_copy)}}" width="100px" height="100px">
            @endif
            </td>
          </tr>
          <tr><th>Remarks</th><td>{!!$purchase_transactions->remarks!!}</td></tr>
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