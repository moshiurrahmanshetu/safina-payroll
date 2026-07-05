@extends('layouts.admin')
@section('title', 'Mrs Item List')
@section('css')
<link href="{{asset('public/css/select2.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-2"><h3>Mrs Item List </h3></div>
  <div class="col-sm-12 col-md-8"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>
          <td>{{Form::text('mrs_no',$search_array['mrs_no'],array('class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=> 'Mrs. No.'))}} </td>
          <td>{{Form::select('user_id',array(''=>'Select User Name')+$users, $search_array['user_id'], array('class' => 'form-control select-search'))}}</td>
          <td> {{Form::text('start_date',$search_array['start_date'],array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))}} </td> 
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
<div class="col-sm-12 col-md-2"><h1>
  {{link_to_route('mrs_item.create','New Mrs Item',[],array('class'=>'btn btn-success pull-right'))}} </h1>
</div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <td>#</td>
            <th>MRS No.</th>
            <th>Item Name</th>
            <th>Purpose Type</th>
            <th>Purpose Name</th>
            <th>Quantity</th>
            <th>Received Date</th>
            <th>Received From</th>
            <th>Received By</th>
            <th>Item Condition</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp  
          @foreach($mrs_items as $data)
          <tr>
            <td>{{$i}}</td>
            <td>{{$data->id}}</td>
            <td>{{$data->name}}
            @php $combinations=json_decode($data->combinations, true); @endphp
            @if($combinations!='')
              @foreach($combinations as $key => $values)
                <br><b>{{$key}}</b>: {{$values}}
              @endforeach
            @endif
            </td>
            <td>{{ config('myhelpers.purpose_type')[$data->requisition->purpose_type] }}</td>
            <td>{{$data->requisition->purpose->name}}</td>
            <td>{!!$data->quantity+0!!}</td>
            <td>{{date('d-m-Y',strtotime($data->received_date))}}</td>
            <td>{!!$users[$data->user_id]!!}</td>
            <td>{!!$users[$data->received_by]!!}</td>
            <td><strong>{{config('myhelpers.item_condition')[$data->item_condition]}}</strong></td>
            <td>
              {!!HTML::decode(link_to_route('mrs_item.show', '<i class="nav-icon icon-eye"></i>', array($data->id)))!!}
            </td>
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
<script src="{{asset('public/js/select2.min.js')}}"></script>
<script>
  $(document).ready(function() {
    $('.select-search').select2();
  });
</script>
@endsection