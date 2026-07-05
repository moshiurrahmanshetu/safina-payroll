@extends('layouts.admin')
@section('title', 'Stock Summary List')
@section('css')
<style type="text/css">
  @media print {
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
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>Stock Summary List</h3></div>
  <div class="col-sm-12 col-md-8"><h3 class="no-print">
    <form action="" method="GET" role="search" >
    <table class="table table-borderless">
        <tr>  
          <td> {{Form::text('start_date',$search_array['start_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
           <td> {{Form::text('end_date',$search_array['end_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))}} </td>
          <td> 
            {{Form::select('item_id',array(''=>'Select Item Name')+$items,$search_array['item_id'],array('class' => 'form-control'))}}
          </td>
          <td> 
            {{Form::select('department_id',array(''=>'Select Departments')+$departments, $search_array['department_id'],array('class' => 'form-control'))}}
          </td> 
           <td><span class="input-group-btn">   
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit">
              <span class="fa fa-search"></span>
            </button>
            </span>
          </td>

       </tr>
      </table> 
    </form> </h3>
  </div>
  <div class="col-sm-12 col-md-1">
    <h3 class="no-print"><br><a class="btn btn-info pull-right txt_white" onclick="print_this_page();" href="javascript:void(0);">Print</a></h3>
  </div>
</div>

<div class="row print-area">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <td>#</td>
            <th>Product Name</th>
            <th>Combinations</th>
            <th>StockIn Quantity</th>
            <th>StockOut Quantity</th>
            <th>Balance</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; $total_qty=0; $total_out_qty=0; @endphp  
          @foreach ($stock_ins as $data)
          @php $total_qty+=$data->total_qty; $balance=$data->total_qty+0; @endphp
          <tr>
            <td>{{$i}}</td>
            <td>{!!$data->item->name!!} 
            </td>
            <td>
              @php $combinations=json_decode($data->combinations, true); @endphp
              @if($combinations!='')
                @foreach($combinations as $key => $values)
                  <b>{{$key}}</b>: {{$values}} <br>
                @endforeach
              @endif
            </td>
            <td>{{$data->total_qty+0}} {!!$data->item->measuring_unit!!}</td>
            <td>
            @if(array_key_exists($data->item_id.'__'.$data->combinations,$stock_out_items))
              {{$stock_out_items[$data->item_id.'__'.$data->combinations]+0}} 
              @php $balance=$balance-$stock_out_items[$data->item_id.'__'.$data->combinations]; $total_out_qty+=$stock_out_items[$data->item_id.'__'.$data->combinations]; @endphp
            @else
              0
            @endif
              {!!$data->item->measuring_unit!!}</td>
            <td>{!!$balance!!} {!!$data->item->measuring_unit!!}</td>
          </tr>
          @php $i=$i+1; @endphp
          @endforeach
        </tbody>
        <thead>
        <tr>
          <th colspan='11' class="txt_white_back_green" > Total In Quantity: {{$total_qty}} , Total Out Quantity: {{$total_out_qty}} , Total Balance: {{$total_qty-$total_out_qty}} </th> 
        </tr>
        </thead>
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