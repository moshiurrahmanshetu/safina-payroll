@extends('layouts.admin')
@section('title', 'Parking Ticket Details')
@section('content')
<h3 class="page-header">Parking Ticket Details {{link_to_route('parking_tickets.index','Back to List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5>QR Code - Scan for Check-in/Check-out</h5>
      </div>
      <div class="card-body text-center">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('parking_tickets.scan', $parking_ticket->ticket_number)) }}" alt="Parking Ticket QR Code" style="width: 200px; height: 200px;">
        <p class="text-muted mt-2">Scan this QR code to check-in or check-out</p>
        {{-- <p><small>{{ route('parking_tickets.scan', $parking_ticket->ticket_number) }}</small></p> --}}
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header">
        <h5>Ticket Information</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <tr>
            <th style="width: 30%">Ticket Number</th>
            <td><strong>{{ $parking_ticket->ticket_number }}</strong></td>
          </tr>
          <tr>
            <th>Vehicle Type</th>
            <td>
              <span class="badge badge-info">{{ $parking_ticket->vehicle->name ?? 'N/A' }}</span>
            </td>
          </tr>
          <tr>
            <th>Vehicle Number</th>
            <td>{{ $parking_ticket->vehicle_number }}</td>
          </tr>
          <tr>
            <th>Driver Name</th>
            <td>{{ $parking_ticket->driver_name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Driver Phone</th>
            <td>{{ $parking_ticket->driver_phone ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>
              @if($parking_ticket->status == 'pending')
                <span class="badge badge-secondary">Pending</span>
              @elseif($parking_ticket->status == 'checked_in')
                <span class="badge badge-success">Checked In</span>
              @elseif($parking_ticket->status == 'checked_out')
                <span class="badge badge-dark">Checked Out</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Hourly Rate</th>
            <td>{{ number_format($parking_ticket->hourly_rate, 2) }} Tk</td>
          </tr>
          <tr>
            <th>Entry Time</th>
            <td>{{ $parking_ticket->entry_time ? $parking_ticket->entry_time->format('d-m-Y H:i:s') : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Exit Time</th>
            <td>{{ $parking_ticket->exit_time ? $parking_ticket->exit_time->format('d-m-Y H:i:s') : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Total Duration</th>
            <td>
              @if($parking_ticket->total_hours)
                {{ $parking_ticket->total_hours }} hours
                @if($parking_ticket->total_minutes)
                  ({{ $parking_ticket->total_minutes }} minutes)
                @endif
              @else
                N/A
              @endif
            </td>
          </tr>
          <tr>
            <th>Total Amount</th>
            <td>
              @if($parking_ticket->total_amount)
                <strong>{{ number_format($parking_ticket->total_amount, 2) }} Tk</strong>
              @else
                N/A
              @endif
            </td>
          </tr>
          <tr>
            <th>Created By</th>
            <td>{{ $parking_ticket->creator->name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ $parking_ticket->created_at->format('d-m-Y H:i:s') }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5>Quick Actions</h5>
      </div>
      <div class="card-body">
        @if($parking_ticket->status == 'pending')
          {{ Form::open(['route' => ['parking_tickets.checkin', $parking_ticket->ticket_number], 'method' => 'POST']) }}
            <button type="submit" class="btn btn-success btn-block mb-2">
              <i class="fa fa-sign-in fa-lg"></i> Check In Now
            </button>
          {{ Form::close() }}
        @elseif($parking_ticket->status == 'checked_in')
          {{ Form::open(['route' => ['parking_tickets.checkout', $parking_ticket->ticket_number], 'method' => 'POST']) }}
            <button type="submit" class="btn btn-warning btn-block mb-2">
              <i class="fa fa-sign-out fa-lg"></i> Check Out Now
            </button>
          {{ Form::close() }}
        @elseif($parking_ticket->status == 'checked_out')
          <a href="{{ route('parking_tickets.receipt', $parking_ticket->ticket_number) }}" class="btn btn-primary btn-block mb-2">
            <i class="fa fa-file-text fa-lg"></i> View Receipt
          </a>
          <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> Parking completed successfully
          </div>
        @endif

        <a href="{{ route('parking_tickets.scan', $parking_ticket->ticket_number) }}" class="btn btn-info btn-block mb-2">
          <i class="fa fa-qrcode"></i> Open Scan Page
        </a>

        {{ link_to_route('parking_tickets.index', 'Back to List', [], ['class' => 'btn btn-primary btn-block']) }}
      </div>
    </div>

    @if($parking_ticket->status == 'checked_out' && $parking_ticket->total_amount)
    <div class="card mt-3 border-success">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">Payment Summary</h5>
      </div>
      <div class="card-body text-center">
        <h2 class="text-success">{{ number_format($parking_ticket->total_amount, 2) }} Tk</h2>
        <p class="mb-0">{{ $parking_ticket->total_hours }} hours @ {{ number_format($parking_ticket->hourly_rate, 2) }} Tk/hour</p>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

