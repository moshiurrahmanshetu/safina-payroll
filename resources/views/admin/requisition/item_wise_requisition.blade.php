@extends('layouts.admin')
@section('title', 'Item Wise Requisition Summary')
@section('css')
<link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet" />
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
  <div class="col-sm-12 col-md-3"><h3>Item Wise Requisition Summary </h3></div>
  <div class="col-sm-12 col-md-8"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>
          <td>{{Form::select('user_id',array(''=>'Select User Name')+$users,$search_array['user_id'], array('class' => 'form-control select-search'))}}</td>
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
  <div class="col-sm-12 col-md-12">
    <h3 class="text-center">Item Wise Requisition Summary: 
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
            <th>Item Name</th>
            <th>Given Quantity</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp  
          @foreach($requisition_items as $data)
            @if($data->given_quantity!=0)
            <tr>
              <td>{{$i}}</td>
              <td>{{$data->item->name}}</td>
              <td>{{$data->given_quantity}} {{$data->item->measuring_unit}}</td>
            </tr>
            @php $i=$i+1; @endphp
            @endif
          @endforeach
        </tbody>

      </table>
    </div>
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('public/js/select2.min.js')}}"></script>
<script>
  $(document).ready(function() {
    $('.select-search').select2();
  });
  function print_this_page(){
    window.print();
  }
</script>
@endsection