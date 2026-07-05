@extends('layouts.admin')
@section('title', 'Report of StockOut Details')
@section('css')
<style type="text/css">
  .only-print{
    display: none;
  }
  @media print {
    .only-print{
      display: block;
      padding-top: 15px;
      padding-bottom: 10px;
    }
    .print-area{
      border: 2px solid #000;
      padding: 5px;
    }
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
<div class="row page-header no-print">
  <div class="col-sm-12 col-md-3"><h3>Report of StockOut Details </h3></div>
  <div class="col-sm-12 col-md-8"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>
          <td>{{Form::select('user_id',array(''=>'Select User Name')+$users,$search_array['user_id'], array('class' => 'form-control'))}}</td>
          <td> {{Form::text('start_date',$search_array['start_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'StockOut Start Date'))}} </td> 
          <td> {{Form::text('end_date',$search_array['end_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'StockOut End Date'))}} </td>
          <td><span class="input-group-btn">
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit">
              <span class="fa fa-search"></span>
            </button></span>
          </td>
        </tr>
      </table> 
    </form></h3>
  </div>
  <div class="col-sm-12 col-md-1">
    <h3 class="no-print"><a class="btn btn-info pull-right txt_white" onclick="print_this_page();" href="javascript:void(0);">Print</a></h3>
  </div>
</div>

<div class="row print-area">
  <div class="col-sm-12 col-md-12 only-print">
    <h3 class="text-center">Report of StockOut Details: 
    @if($search_array['user_id']!='') for {{$users[$search_array['user_id']]}} @endif
    @if($search_array['start_date']!='') from {{$search_array['start_date']}} @endif
    @if($search_array['end_date']!='') to {{$search_array['end_date']}} @endif
  </h3>
  </div>
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <td>#</td>
            <th>Req. No.</th>
            <th>Requested By</th>
            <th>Purpose Type</th>
            <th>Purpose Name</th>
            <th>Req. Date</th>
            <th>StockOut Date</th>
            <th>Requested Items</th>
            <th>Given Items</th>
            <th>Status</th>
            <th>Given By</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp  
          @foreach ($requisitions as $data)
          <tr>
            <td>{{$i}}</td>
            <td>{{$data->id}}</td>
            <td>{!!$users[$data->user_id]!!}</td>
            <td>{{ config('myhelpers.purpose_type')[$data->purpose_type] }}</td>
            <td>{{$data->purpose->name}}</td>
            <td>{{date('d-m-Y',strtotime($data->created_at))}}</td>
            <td>@if($data->stock_out_date) {{date('d-m-Y',strtotime($data->stock_out_date))}} @endif</td>
            <td>
              @foreach($data->requisition_items as $items)
                {!!$items->name!!} ({!!$items->req_quantity+0!!})<br> 
              @endforeach
            </td>
            <td>
              @foreach($data->requisition_items as $items)
                {!!$items->name!!} ({!!$items->given_quantity+0!!})<br> 
              @endforeach
            </td>
            <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.requisition_status')[$data->status]}}</strong>
              @if($data->received_status=='received')
              <strong class="btn-success"> &#10003;</strong>
              @endif
            </td>
            <td>{!!$data->given_by!!}</td>
          </tr>
          @php $i=$i+1; @endphp
          @endforeach
        </tbody>

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