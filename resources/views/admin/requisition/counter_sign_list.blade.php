@extends('layouts.admin')
@section('title', 'Counter Sign List')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>Counter Sign List </h3></div>
  <div class="col-sm-12 col-md-8"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>
          <td> {{Form::text('req_no',$search_array['req_no'],array('class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=> 'Req. No.'))}} </td>
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
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <td>#</td>
            <th>Req. No.</th>
            <th>Req. Date</th>
            <th>Purpose Type</th>
            <th>Purpose Name</th>
            <th>Requested By</th>
            <th>Counter sign status</th>
            <th>Requested Items</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp  
          @foreach ($requisitions as $data)
          <tr>
            <td>{{$i}}</td>
            <td>{{$data->id}}</td>
            <td>{{date('d-m-Y',strtotime($data->created_at))}}</td>
            <td>{{ config('myhelpers.purpose_type')[$data->purpose_type] }}</td>
            <td>{{$data->purpose->name}}</td>
            <td>{!!$users[$data->user_id]!!}</td>
            <td><strong class="btn-{{ config('myhelpers.status_color')[$data->counter_sign_status] }}">{{config('myhelpers.counter_sign_status')[$data->counter_sign_status]}}</strong></td> 
            <td>
              @foreach($data->requisition_items as $items)
                {!!$items->name!!} ({!!$items->req_quantity+0!!})<br> 
              @endforeach
            </td>
            <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.requisition_status')[$data->status]}}</strong>
              @if($data->received_status=='received')
              <strong class="btn-success"> &#10003;</strong>
              @endif
            </td>
            <td>
              {!!HTML::decode(link_to_route('counter_sign_show', '<i class="nav-icon icon-eye"></i>', array($data->id)))!!}
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