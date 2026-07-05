@extends('layouts.admin')
@section('title', 'Parking Ticket List')
@section('content')
<h3 class="page-header">
  Parking Ticket List @if($parking_tickets) ({{count($parking_tickets)}}) @endif
  <div class="pull-right">
    {{link_to_route('parking_tickets.scan_camera','Scan with Camera',[],array('class'=>'btn btn-info mr-2'))}}
    {{link_to_route('parking_tickets.create','Create Parking Ticket',[],array('class'=>'btn btn-success'))}}
  </div>
</h3>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        {{ Form::open(['route' => 'parking_tickets.index', 'method' => 'GET', 'class' => 'form-inline']) }}
        <div class="row">
          <div class="col-md-{{ $canViewAll ? '2' : '3' }}">
            <div class="form-group">
              <label>From Date</label>
              {{ Form::date('from_date', request('from_date'), ['class' => 'form-control']) }}
            </div>
          </div>
          <div class="col-md-{{ $canViewAll ? '2' : '3' }}">
            <div class="form-group">
              <label>To Date</label>
              {{ Form::date('to_date', request('to_date'), ['class' => 'form-control']) }}
            </div>
          </div>
          <div class="col-md-{{ $canViewAll ? '2' : '3' }}">
            <div class="form-group">
              <label>Status</label>
              {{ Form::select('status', ['' => 'All', 'pending' => 'Pending', 'checked_in' => 'Checked In', 'checked_out' => 'Checked Out'], request('status'), ['class' => 'form-control']) }}
            </div>
          </div>
          @if($canViewAll)
          <div class="col-md-2">
            <div class="form-group">
              <label>Created By</label>
              {{ Form::select('user_id', ['' => 'All Users'] + $users, request('user_id'), ['class' => 'form-control']) }}
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>Parking Counter</label>
              {{ Form::select('parking_counter_id', ['' => 'All Counters'] + $counters, request('parking_counter_id'), ['class' => 'form-control']) }}
            </div>
          </div>
          @endif
          <div class="col-md-{{ $canViewAll ? '2' : '3' }}">
            <div class="form-group" style="margin-top: 24px;">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-filter"></i> Filter
              </button>
              <a href="{{ route('parking_tickets.index') }}" class="btn btn-danger">
                <i class="fa fa-refresh"></i> Reset
              </a>
            </div>
          </div>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="card">
      <div class="card-body">
        <table class="table table-striped table-bordered" id="parkingTicketsTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Ticket Number</th>
              <th>QR Code</th>
              <th>Vehicle</th>
              <th>Vehicle Number</th>
              <th>Driver</th>
              <th>Parking Counter</th>
              <th>Status</th>
              <th>Hourly Rate</th>
              <th>Total Amount</th>
              <th>Created By</th>
              <th>Created At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($parking_tickets as $ticket)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td><strong>{{ $ticket->ticket_number }}</strong></td>
              <td class="text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode(route('parking_tickets.scan', $ticket->ticket_number)) }}" alt="QR" style="width: 50px; height: 50px;">
                <br>
                <a href="{{ route('parking_tickets.scan', $ticket->ticket_number) }}" class="btn btn-xs btn-info mt-1">Scan</a>
              </td>
              <td>
                <span class="badge badge-info">{{ $ticket->vehicle->name ?? 'N/A' }}</span>
              </td>
              <td>{{ $ticket->vehicle_number }}</td>
              <td>
                {{ $ticket->driver_name ?? 'N/A' }}
                @if($ticket->driver_phone)
                  <br><small>{{ $ticket->driver_phone }}</small>
                @endif
              </td>
              <td>
                @if($ticket->parkingCounter)
                  <span class="badge badge-primary">{{ $ticket->parkingCounter->name }}</span>
                @else
                  <span class="badge badge-secondary">No Counter</span>
                @endif
              </td>
              <td>
                @if($ticket->status == 'pending')
                  <span class="badge badge-secondary">Pending</span>
                @elseif($ticket->status == 'checked_in')
                  <span class="badge badge-success">Checked In</span>
                @elseif($ticket->status == 'checked_out')
                  <span class="badge badge-dark">Checked Out</span>
                @endif
              </td>
              <td>{{ number_format($ticket->hourly_rate, 2) }} Tk</td>
              <td>
                @if($ticket->total_amount)
                  {{ number_format($ticket->total_amount, 2) }} Tk
                @else
                  -
                @endif
              </td>
              <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
              <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
              <td>
                {{ link_to_route('parking_tickets.show', 'View', [$ticket->ticket_number], ['class' => 'btn btn-sm btn-info']) }}
                <a href="{{ route('parking_tickets.scan', $ticket->ticket_number) }}" class="btn btn-sm btn-success">Scan</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
  $('#parkingTicketsTable').DataTable({
    "order": [[0, "desc"]],
    "pageLength": 25
  });
});
</script>
@endsection
