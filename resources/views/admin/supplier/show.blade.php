@extends('layouts.admin')
@section('title', 'Supplier Details')
@section('content')

<div class="row page-header">
  <div class="col-sm-12 col-md-3">
    <h3 class="txt_green"> Supplier Details</h3>
  </div>
 
  <div class="col-sm-12 col-md-9">
    <form action="" method="GET" role="search" >
    <table class="table table-borderless">
        <tr>  
          <td> {{Form::text('start_date',$search_array['start_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
           <td> {{Form::text('end_date',$search_array['end_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))}} </td>
           <td><span class="input-group-btn">   
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit">
              <span class="fa fa-search"></span>
            </button>
            </span>
          </td>
          <td> 
        <!-- <a class="btn btn-info pull-right txt_white" target="_blank" href="#">Print</a> -->
    </td> 
    <td>{{link_to_route('supplier.index','Supplier List',[],array('class'=>'btn btn-success pull-right'))}}</td>
       </tr>
      </table> 
    </form>
  </div> 

</div>
<div class="row">
  <div class="col-sm-12 col-md-12 multi-column">
    <div class="col-sm-12 col-md-6">
      <table class="table table-striped">
        <tr><th>Contact Name</th> <td>{!!$suppliers->contact_name!!}</td></tr>
        <tr><th>Company Name</th> <td>{!!$suppliers->company_name!!}</td></tr>
        <tr><th>Mobile No</th><td>{{$suppliers->mobile}}</td></tr>
        <tr><th>E-Mail Address</th><td>{{$suppliers->email}}</td></tr>
        <tr><th>Status</th><td><strong class="btn-{{ config('myhelpers.status_color')[$suppliers->status] }}">{{config('myhelpers.status')[$suppliers->status]}}</strong></td></tr>
        <tr><th>Supplier Type</th><td> {{config('myhelpers.supplier_type')[$suppliers->supplier_type]}}</td></tr>
      </table>
    </div>
      <div class="col-sm-12 col-md-6"> 
      <table class="table table-striped">
        <tr><th>Address</th> <td>{{$suppliers->address}}</td></tr>
        <tr><th>Web Site</th><td>{{$suppliers->web_site}}</td> </tr>
        <tr><th>Created By</th><td>{!!$users[$suppliers->created_by]!!}</td></tr>
        <tr><th>Updated By</th><td>{!!$users[$suppliers->updated_by]!!}</td></tr> 
        <tr><th>Created At</th><td>{{date('d-m-Y',strtotime($suppliers->created_at))}}</td></tr>
        <tr><th>Updated At</th><td>{{date('d-m-Y',strtotime($suppliers->updated_at))}}</td></tr>

      </table>
    </div>
  </div>
</div>

<div class="row">
   <h3 class="txt_green">Purchasing Details from {{date('d-F-Y', strtotime($search_array['start_date'])) }} to {{date('d-F-Y', strtotime($search_array['end_date'])) }} </h3>
  <div class="col-sm-12 col-md-12 multi-column">
    <table class="table table-striped">
        <thead>
          <tr>
            <td>#</td>
            <th>Purchase Date</th> 
            <th>Items</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp
          @foreach ($purchasing_details as $data)
          @if(($data->purchase_date>=$search_array['start_date'])&&($data->purchase_date<=$search_array['end_date']))
          <tr>
            <td>{{$i}}</td>
            <td>{{date('d-m-Y',strtotime($data->purchase_date))}}</td> 
            <td>
              @foreach($data->purchase_items as $items)
                {!!$items->name!!} ({!!$items->quantity+0!!})<br> 
              @endforeach
            </td>
            <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          </tr>
          @php $i=$i+1; @endphp
          @endif
          @endforeach
        </tbody>
      </table>
  </div>
</div>

@endsection