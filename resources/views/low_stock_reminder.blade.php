@extends('layouts.admin')

@section('content')
<div class="animated fadeIn">
  <div class="row">
    <div class="col-sm-12 col-md-12"><h3>Low Stock Reminder All List</h3>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <td>#</td>
              <th>Product Name</th>
              <th>StockIn Qty</th>
              <th>StockOut Qty</th>
              <th>Used Stock Qty</th>
              <th>Balance</th>
              <th class="text-center">Low Stock Value</th>
            </tr>
          </thead>
          <tbody>
            @php $i=1; $stock_out=0; @endphp  
            @foreach ($stock_ins as $data)
            @php $used_stock=0; 
            if(array_key_exists($data->item_id,$stock_out_items)){
              $balance=$data->total_qty-$stock_out_items[$data->item_id];
              $stock_out=$stock_out_items[$data->item_id];
            }else{
              $balance=$data->total_qty+0; $stock_out=0;
            }
            if(array_key_exists($data->item_id,$mrs_balance)){
              $balance+=$mrs_balance[$data->item_id]; $used_stock=$mrs_balance[$data->item_id];
            }
            if($balance<=$data->item->low_stock){
            @endphp
            <tr>
              <td>{{$i}}</td>
              <td>{!!$data->item->name!!}</td>
              <td>{{$data->total_qty+0}} {!!$data->item->measuring_unit!!}</td>
              <td> {{$stock_out}} {!!$data->item->measuring_unit!!}</td>
              <td> {{$used_stock}} {!!$data->item->measuring_unit!!}</td>
              <td>{!!$balance!!} {!!$data->item->measuring_unit!!}</td>
              <td class="text-center">{!!$data->item->low_stock!!}</td>
            </tr>
            @php $i=$i+1; } @endphp
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>


@endsection
