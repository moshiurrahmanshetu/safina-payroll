@extends('layouts.admin')
@section('title', 'Requisition List')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>Requisition List </h3></div>
  <div class="col-sm-12 col-md-7"><h3>
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
        @if($search_array['start_date']!='')
        <td> 
          <!-- <a class="btn btn-info pull-right txt_white" target="_blank" href="{{ route('purchase_print',['download'=>'purchase','start_date'=>$search_array['start_date'],'end_date'=>$search_array['end_date']]) }}">Print</a> -->
        </td>
        @endif
      </tr>
    </table> 
  </form> </h3>
</div>
<div class="col-sm-12 col-md-2"><h1>
  {{link_to_route('requisition.create','New Requisition',[],array('class'=>'btn btn-success pull-right'))}} </h1>
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
            <th>Counter Sign By</th>
            <th>Counter sign status</th>
            <th>Requested Items</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp  
          @foreach($requisitions as $data)
          <tr>
            <td>{{$i}}</td>
            <td>{{$data->id}}</td>
            <td>{{date('d-m-Y',strtotime($data->created_at))}}</td>
            <td>{{ config('myhelpers.purpose_type')[$data->purpose_type] }}</td>
            <td>{{$data->purpose->name}}</td>
            <td>{!!$users[$data->counter_sign_by]!!}</td>
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
              {!!HTML::decode(link_to_route('requisition.show', '<i class="nav-icon icon-eye"></i>', array($data->id)))!!}
              @if($data->status==0 && $data->counter_sign_status==0)
              {!!HTML::decode(link_to_route('requisition.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
              {{ Form::open(array('route' => array('requisition.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
              <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
              {{ Form::close() }}
              @endif
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