@extends('layouts.admin')
@section('title', 'Mrs Item Summary')
@section('css')
<link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>Mrs Item Summary </h3></div>
  <div class="col-sm-12 col-md-7"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>  
          <td> {{Form::text('start_date',$search_array['start_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
          <td> {{Form::text('end_date',$search_array['end_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))}} </td>
          <td> 
            {{Form::select('item_id',array(''=>'Select Item Name')+$mrs_item_lists, $search_array['item_id'], array('class' => 'form-control select-search'))}}
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

</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <td>#</td>
            <th>Product Name</th>
            <th>Combinations</th>
            <th>Received Quantity</th>
            <th>StockOut Quantity</th>
            <th>Balance</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; $total_qty=0; $total_out_qty=0; @endphp  
          @foreach($mrs_items as $data)
          @php $total_qty+=$data->total_qty; $balance=$data->total_qty+0; @endphp
          <tr>
            <td>{{$i}}</td>
            <td>{{$mrs_item_lists[$data->item_id]}}
            </td>
            <td></td>
            <td>{!!$data->total_qty+0!!}</td>
            <td>
            @if(array_key_exists($data->item_id,$stock_out_items))
              {{$stock_out_items[$data->item_id]+0}} 
              @php $balance=$balance-$stock_out_items[$data->item_id]; $total_out_qty+=$stock_out_items[$data->item_id]; @endphp
            @else
              0
            @endif
            </td>
            <td>{!!$balance!!}</td>
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
<script src="{{asset('public/js/select2.min.js')}}"></script>
<script>
  $(document).ready(function() {
    $('.select-search').select2();
  });
</script>
@endsection