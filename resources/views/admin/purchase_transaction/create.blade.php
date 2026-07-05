@extends('layouts.admin')
@section('title', 'Purchase Transaction')
@section('content')
<h3 class="page-header">Purchase Transaction Create{{link_to_route('purchase_transaction.index','Transaction List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model(Request::old(),array('route' => array('purchase_transaction.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  @php $date=date("d-m-Y"); @endphp
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label"><h5><b>Purchase Info: {!! HTML::decode(link_to_route('purchase.edit', '<i class="nav-icon icon-eye"></i>', array($purchase->id)))!!}</b></h5></label><br>
        <b>Purchase Date:</b> {{date('d-m-Y',strtotime($purchase->purchase_date))}}<br>
        <b>Purchase Price:</b> {{$purchase->grand_total+0}}<br>
        <b>Purchase Status:</b> {{config('myhelpers.purchase_status')[$purchase->status]}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label"><h5><b>Supplier Info: {!! HTML::decode(link_to_route('supplier.edit', '<i class="nav-icon icon-eye"></i>', array($purchase->supplier_id)))!!}</b></h5></label><br>
        <b>Name:</b> {{$purchase->supplier->contact_name}} ({{$purchase->supplier->company_name}})<br>
        <b>Mobile#:</b> {{$purchase->supplier->mobile}}<br>
        <b>Email:</b> {{$purchase->supplier->email}}
        {{Form::hidden('supplier_id',$purchase->supplier_id)}}
        {{Form::hidden('purchase_id',$purchase->id)}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label"><h5><b>Transaction Info: {!! HTML::decode(link_to_route('purchase_transaction.index', '<i class="nav-icon icon-eye"></i>', ['purchase_id'=>$purchase->id,'BTSubmit'=>'']))!!}</b></h5></label><br>
        @foreach($transactions as $item)
        <b>Total Paid:</b> {{$item->total+0}}<br>
        <b>Total Due:</b> {{$purchase->grand_total-$item->total}}<br>
        <b>Last Paid:</b> {{$transaction_last->amount+0}} / {{date('d-m-Y',strtotime($transaction_last->payment_date))}}
        @endforeach
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Payment Type <sup>*</sup></label>
        {{Form::select('payment_type',config('myhelpers.payment_type'),null,array('class' => 'form-control','required'=>'required'))}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Transaction Person <sup>*</sup></label>
        {{Form::select('given_by',array(''=>'Select One')+$users,null, array('class' => 'form-control','required'=>'required'))}} 
        {!! $errors->first('given_by', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div> 
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Received By </label>
        {{Form::text('received_by',null, array('class' => 'form-control'))}}
        {!! $errors->first('received_by', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column"> 
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Money Rceipt No </label>
        {{Form::text('money_rceipt_no',null, array('class' => 'form-control'))}}
        {!! $errors->first('money_rceipt_no', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Amount <sup>*</sup></label>
        {{Form::number('amount',null, array('class' => 'form-control','step'=>'any', 'required'=>'required'))}}
        {!! $errors->first('amount', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Transaction Date <sup>*</sup> </label>
        {{Form::text('payment_date',$date, array('class' => 'form-control datetimepicker1', 'required'=>'required'))}}
        {!! $errors->first('payment_date', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="col-md-7 form-group">
        <label class="control-label">Photo <sub>(max 245kb image,jpeg,png,jpg)</sub></label>
          {{Form::file('attachment_copy',array('class' => 'form-control', 'onChange'=>'readURL(this)', 'accept'=>'.png,.jpg,.jpeg'))}}  
          {!! $errors->first('attachment_copy', '<p class="text-danger">:message</p>' ) !!} 
      </div>
      <div class="col-md-5 preview-div">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Remarks</label>
        {{Form::textarea('remarks',null, array('class' => 'form-control','rows'=>3 ))}}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Create Purchase Transaction
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection