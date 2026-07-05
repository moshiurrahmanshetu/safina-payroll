@extends('layouts.admin')
@section('title', 'My Mrs Item List')
@section('content')
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>My Mrs Item List </h3></div>
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
            <th>MRS No.</th>
            <th>Item Name</th>
            <th>Purpose Type</th>
            <th>Purpose Name</th>
            <th>Quantity</th>
            <th>Received Date</th>
            <th></th>
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
            <td></td>
            <td>{!!$users[$data->received_by]!!}</td>
            <td><strong>{{config('myhelpers.item_condition')[$data->item_condition]}}</strong></td>
            <td>
              {!!HTML::decode(link_to_route('my_mrs_item_show', '<i class="nav-icon icon-eye"></i>', array($data->id)))!!}
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