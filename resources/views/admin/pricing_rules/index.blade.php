@extends('layouts.admin')
@section('title', 'Pricing Rule List')
@section('content')
<h3 class="page-header">Pricing Rule List {{link_to_route('pricing_rules.create','Add Pricing Rule',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <th>#</th>
            <th>Service</th>
            <th>Rule Type</th>
            <th>Date/Period</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp 
         @foreach ($pricing_rules as $data) 
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->service ? $data->service->name : 'N/A'}}</td>
          <td>{{config('myhelpers.rule_type')[$data->rule_type]}}</td>
          <td>
            @if($data->rule_type == 1 && $data->days)
              @php $days = is_array($data->days) ? $data->days : json_decode($data->days, true); @endphp
              {{ is_array($days) ? implode(', ', $days) : $data->days }}
            @else
              {{$data->start_date ? $data->start_date : '-'}} 
              @if($data->end_date && $data->end_date != $data->start_date) to {{$data->end_date}} @endif
            @endif
          </td>
          <td>{{$data->amount}} {{$data->price_type == 1 ? '%' : '(Fixed)'}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
            {!! HTML::decode(link_to_route('pricing_rules.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
            {{ Form::open(array('route' => array('pricing_rules.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form-'.$data->id)) }}
            <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
            {{ Form::close() }}
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

@endsection
