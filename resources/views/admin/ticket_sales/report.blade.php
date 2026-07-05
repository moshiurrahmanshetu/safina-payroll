@extends('layouts.admin')
@section('title', 'Ticket Sales Report')
@section('content')
<h3 class="page-header">Ticket Sales Report {{link_to_route('ticket_sales.index','Ticket Sale List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'ticket_sales.report', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>From Date:</label>
          <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>To Date:</label>
          <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
      </div>
      <div class="col-md-3">
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
      <div class="col-md-3">
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
    </div>
    <div class="row">
      <div class="col-md-3">
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
      <div class="col-md-6">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Generate Report</button>
            <a href="{{ route('ticket_sales.report') }}" class="btn btn-danger">Reset</a>
          </div>
        </div>
      </div>
    </div>
    {{ Form::close() }}
  </div>
</div>
<br>

<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
  <table class="w-100 min-w-full text-sm text-center text-gray-700 border-collapse">
    
    <!-- Header -->
    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
      <tr>
        <th class="px-6 py-3 border border-gray-300">Gross Amount</th>
        <th class="px-6 py-3 border border-gray-300">Total Discount</th>
        <th class="px-6 py-3 border border-gray-300">Final Amount</th>
        <th class="px-6 py-3 border border-gray-300">Total Tickets</th>
      </tr>
    </thead>

    <!-- Data -->
    <tbody>
      <tr>
        <td class="px-6 py-4 border border-gray-300 font-semibold text-blue-600">
          {{ number_format($grand_gross, 2) }} Tk
        </td>
        <td class="px-6 py-4 border border-gray-300 font-semibold text-yellow-600">
          {{ number_format($grand_discount, 2) }} Tk
        </td>
        <td class="px-6 py-4 border border-gray-300 font-bold text-green-600">
          {{ number_format($grand_amount, 2) }} Tk
        </td>
        <td class="px-6 py-4 border border-gray-300 font-bold text-indigo-600">
          {{ $grand_quantity }}
        </td>
      </tr>
    </tbody>

  </table>
</div>
<br>
<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <h4>Ticket Sales Summary</h4>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Ticket Name</th>
            <th>Quantity</th>
            <th>Gross Amount</th>
            <th>Discount</th>
            <th>Final Amount</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($report as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->ticket ? $data->ticket->name : 'N/A'}}</td>
          <td>{{$data->total_quantity}}</td>
          <td>{{number_format($data->total_amount, 2)}}</td>
          <td>{{number_format($data->total_discount, 2)}}</td>
          <td>{{number_format($data->total_final_amount, 2)}}</td>
       </tr>
       @php $i=$i+1; @endphp
       @endforeach
       <tr class="success">
         <td colspan="2"><strong>Grand Total</strong></td>
         <td><strong>{{$grand_quantity}}</strong></td>
         <td><strong>{{number_format($grand_gross, 2)}}</strong></td>
         <td><strong>{{number_format($grand_discount, 2)}}</strong></td>
         <td><strong>{{number_format($grand_amount, 2)}}</strong></td>
       </tr>
     </tbody>
   </table>
 </div>



</div>
</div>

@endsection
@section('script')

@endsection
