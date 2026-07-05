@extends('layouts.admin')
@section('title', 'Booking List')
@section('content')
<h3 class="page-header">
  Booking List @if($bookings) ({{count($bookings)}}) @endif
  <div class="pull-right">
    {{link_to_route('bookings.create','Add Booking',[],array('class'=>'btn btn-success'))}}
    {{link_to_route('bookings.counter_report','Counter Report',[],array('class'=>'btn btn-info', 'style'=>'margin-right:10px;margin-left:10px;'))}}
  </div>
</h3>

<!-- Filter Panel -->
<div class="panel panel-default">
  <div class="panel-heading">
    <i class="nav-icon icon-magnifier"></i> Filter Bookings
    <span class="pull-right">
      <a href="{{ route('bookings.index') }}" class="btn btn-xs btn-danger">Reset Filters</a>
    </span>
  </div>
  <div class="panel-body">
    <form method="GET" action="{{ route('bookings.index') }}" class="form-horizontal">
      <div class="row">
        <!-- From Date -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">From Date</label>
            <input type="text" name="from_date" class="form-control datepicker" placeholder="DD-MM-YYYY"
                   value="{{ request('from_date') }}">
          </div>
        </div>

        <!-- To Date -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">To Date</label>
            <input type="text" name="to_date" class="form-control datepicker" placeholder="DD-MM-YYYY"
                   value="{{ request('to_date') }}">
          </div>
        </div>

        @if($canViewAll)
        <!-- User Filter (Admin only) -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">User</label>
            <select name="user_id" class="form-control">
              <option value="">All Users</option>
              @foreach($users as $id => $name)
                <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Counter Filter (Admin only) -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Counter</label>
            <select name="counter_id" class="form-control">
              <option value="">All Counters</option>
              @foreach($counters as $id => $name)
                <option value="{{ $id }}" {{ request('counter_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        @else
        <!-- Limited filters for counter users -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Your Counter</label>
            <select name="counter_id" class="form-control">
              <option value="">All Your Counters</option>
              @foreach($counters as $id => $name)
                <option value="{{ $id }}" {{ request('counter_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        @endif
      </div>

      <div class="row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-primary">
            <i class="nav-icon icon-magnifier"></i> Search
          </button>
          @if($canViewAll)
          <button type="submit" name="generate_report" value="1" class="btn btn-info" formtarget="_blank">
            <i class="nav-icon icon-printer"></i> Print Report
          </button>
          @endif
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Summary Cards -->
<div class="row">
  <div class="col-md-6">
    <div class="panel panel-info">
      <div class="panel-heading">
        <i class="nav-icon icon-list"></i> Total Bookings
      </div>
      <div class="panel-body text-center">
        <h2>{{ $totalBookings }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-success">
      <div class="panel-heading">
        <i class="nav-icon icon-wallet"></i> Total Revenue
      </div>
      <div class="panel-body text-center">
        <h2>৳{{ number_format($totalRevenue, 2) }}</h2>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time Slot</th>
            <th>Guest Name</th>
            <th>Counter</th>
            <th>Created By</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($bookings as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->service->name}}</td>
          <td>{{date('d-m-Y',strtotime($data->check_in_date))}}</td>
          <td>{{$data->time_slot}}</td>
          <td>{{ $data->name ?? 'N/A' }}</td>
          <td>{{$data->counter ? $data->counter->name : '-'}}</td>
          <td>{{$data->creator ? $data->creator->name : '-'}}</td>
          <td>{{$data->final_price}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.booking_status')[$data->status]}}</strong></td>
          <td>
            {!! HTML::decode(link_to_route('bookings.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
            {{ Form::open(array('route' => array('bookings.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form-'.$data->id)) }}
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
<script>
$(document).ready(function(){
  // Initialize datepickers
  $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    todayHighlight: true
  });
});
</script>
@endsection
