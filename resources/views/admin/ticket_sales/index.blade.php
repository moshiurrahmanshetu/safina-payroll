@extends('layouts.admin')
@section('title', 'Ticket Sale List')
@section('content')
<h3 class="page-header">Ticket Sale List @if($ticket_sales) ({{count($ticket_sales)}}) @endif {{link_to_route('ticket_sales.create','Add Ticket Sale',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'ticket_sales.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label>From Date:</label>
          <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>To Date:</label>
          <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>User:</label>
          <select name="user_id" class="form-control">
            <option value="">All Users</option>
            @foreach($users as $id => $name)
              <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Counter:</label>
          <select name="gate_id" class="form-control">
            <option value="">All Counters</option>
            @foreach($gates as $id => $name)
              <option value="{{ $id }}" {{ request('gate_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Ticket:</label>
          <select name="ticket_id" class="form-control">
            <option value="">All Tickets</option>
            @foreach($tickets as $id => $name)
              <option value="{{ $id }}" {{ request('ticket_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('ticket_sales.index') }}" class="btn btn-danger">Reset</a>
          </div>
        </div>
      </div>
    </div>
    {{ Form::close() }}
  </div>
</div>
<br>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Ticket Name</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Total Price</th>
            <th>Counter</th>
            <th>Sold By</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($ticket_sales as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->ticket ? $data->ticket->name : 'N/A'}}</td>
          <td>{{$data->price}}</td>
          <td>
            {{number_format($data->discount_amount, 2)}}
          </td>
          <td>
            {{number_format($data->total_price, 2)}}
          </td>
          <td>{{$data->gate ? $data->gate->name : 'N/A'}}</td>
          <td>{{$data->creator ? $data->creator->name : 'N/A'}}</td>
          <td>{{$data->date ? $data->date->format('d-m-Y') : date('d-m-Y', strtotime($data->created_at))}}</td>
          <td>
            {!! $data->is_used ? '<span class="label label-danger">USED</span>' : '<span class="label label-success">VALID</span>' !!}
          </td>
          <td>
           @if($data->qr_code)
             {!! HTML::decode(link_to_route('ticket_sales.print', '<i class="nav-icon icon-printer"></i>', ['qr_code' => $data->qr_code], array('class'=>'btn btn-info', 'target'=>'_blank')))!!}
           @endif
           @if($data->qr_code)
             {{ Form::open(array('route' => array('ticket_sales.destroy', $data->qr_code), 'method'=>'DELETE', 'id'=>'del-form', 'style'=>'display:inline;')) }}
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
@section('script')

@endsection
