@extends('layouts.admin')
@section('title', 'All Returnable Items List')
@section('css')
<link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>All Returnable Items List </h3></div>
  <div class="col-sm-12 col-md-9"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>
          <td>{{Form::text('req_no',$search_array['req_no'],array('class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=> 'Req. No.'))}} </td>
          <td>{{Form::select('user_id',array(''=>'Select User Name')+$users,$search_array['user_id'], array('class' => 'form-control select-search'))}}</td>
          <td>{{Form::text('start_date',$search_array['start_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
          <td> {{Form::text('end_date',$search_array['end_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))}} </td>
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
            <th>#</th>
            <th>Items Name</th>
            <th>Purpose Type</th>
            <th>Purpose Name</th>
            <th>Returnable Quantity</th>
            <th>Returned Quantity</th>
            <th>Measuring Unit</th>
            <th>Description</th>
            <th>Req. No.</th>
            <th>Req. Date</th>
            <th>Counter Sign By</th>            
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp  
          @foreach ($requisitions as $data)
            @foreach($data->requisition_items as $items)
            @php $mrs_quantity=0; if(count($items->mrs_items)>0){ $mrs_quantity=$items->mrs_items[0]->total_qty; }
            if($items->given_quantity<=$mrs_quantity){ $done='mrs-done'; }else{ $done=''; } @endphp
            <tr class="{{$done}}">
              <td>{{$i}}</td>
              <td>{!!$items->name!!}</td>
              <td>{{ config('myhelpers.purpose_type')[$data->purpose_type] }}</td>
              <td>{{$data->purpose->name}}</td>
              <td>{!!$items->given_quantity+0!!}</td>
              <td>{!!$mrs_quantity+0!!}</td>
              <td>{!!$items->measuring_unit!!}</td>
              <td>{!!$items->description!!}</td>
              <td>{{$data->id}}</td>
              <td>{{date('d-m-Y',strtotime($items->created_at))}}</td> 
              <td>{!!$users[$data->counter_sign_by]!!}</td>
            </tr>
            @php $i=$i+1; @endphp
            @endforeach
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
</script>
@endsection