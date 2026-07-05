@extends('layouts.admin')
@section('title', 'Parking Ticket Scan')
@section('content')
<style>
.scan-card {
  max-width: 600px;
  margin: 0 auto;
}
.action-btn {
  font-size: 1.5rem;
  padding: 20px 40px;
  min-height: 80px;
}
.status-badge {
  font-size: 1.2rem;
  padding: 10px 20px;
}
@media (max-width: 768px) {
  .action-btn {
    font-size: 1.2rem;
    padding: 15px 30px;
    width: 100%;
  }
  .info-table td, .info-table th {
    font-size: 1rem;
  }
}
</style>

<div class="scan-card">
  <div class="card">
    <div class="card-header text-center bg-primary text-white">
      <h4 class="mb-0"><i class="fa fa-qrcode"></i> Scan Result</h4>
      <p class="mb-0">{{ $parking_ticket->ticket_number }}</p>
    </div>
    <div class="card-body">
      <div class="text-center mb-4">
        @if($parking_ticket->status == 'pending')
          <span class="badge badge-secondary status-badge">
            <i class="fa fa-clock-o"></i> Pending - Check-in Required
          </span>
        @elseif($parking_ticket->status == 'checked_in')
          <span class="badge badge-success status-badge">
            <i class="fa fa-check-circle"></i> Checked In
          </span>
        @elseif($parking_ticket->status == 'checked_out')
          <span class="badge badge-dark status-badge">
            <i class="fa fa-check-circle"></i> Completed
          </span>
        @endif
      </div>

      <table class="table table-bordered info-table">
        <tr>
          <th style="width: 40%">Vehicle Type</th>
          <td>
            <span class="badge badge-info">{{ $parking_ticket->vehicle->name ?? 'N/A' }}</span>
          </td>
        </tr>
        <tr>
          <th>Vehicle Number</th>
          <td><strong>{{ $parking_ticket->vehicle_number }}</strong></td>
        </tr>
        <tr>
          <th>Driver</th>
          <td>
            {{ $parking_ticket->driver_name ?? 'N/A' }}
            @if($parking_ticket->driver_phone)
              <br><small>{{ $parking_ticket->driver_phone }}</small>
            @endif
          </td>
        </tr>
        <tr>
          <th>Slot Price</th>
          <td>{{ number_format($parking_ticket->base_price ?? $parking_ticket->hourly_rate, 2) }} Tk <small class="text-muted">(08:00 - 18:00)</small></td>
        </tr>
        @if($parking_ticket->entry_time)
        <tr>
          <th>Entry Time</th>
          <td>{{ $parking_ticket->entry_time->format('d-m-Y H:i:s') }}</td>
        </tr>
        @endif
        @if($parking_ticket->exit_time)
        <tr>
          <th>Exit Time</th>
          <td>{{ $parking_ticket->exit_time->format('d-m-Y H:i:s') }}</td>
        </tr>
        @endif
        @if($parking_ticket->slot_multiplier)
        <tr>
          <th>Slots Used</th>
          <td><strong>{{ $parking_ticket->slot_multiplier }} slot(s)</strong></td>
        </tr>
        @endif
        @if($parking_ticket->paid_amount)
        <tr class="table-info">
          <th>Paid at Entry</th>
          <td>{{ number_format($parking_ticket->paid_amount, 2) }} Tk</td>
        </tr>
        @endif
        @if($parking_ticket->extra_amount > 0)
        <tr class="table-warning">
          <th>Extra Payment</th>
          <td><strong class="text-warning">{{ number_format($parking_ticket->extra_amount, 2) }} Tk</strong></td>
        </tr>
        @endif
        @if($parking_ticket->total_amount)
        <tr class="table-success">
          <th>Total Amount</th>
          <td><strong class="text-success">{{ number_format($parking_ticket->total_amount, 2) }} Tk</strong></td>
        </tr>
        @endif
      </table>

      <div class="text-center mt-4">
        @if($parking_ticket->status == 'pending')
          <div class="alert alert-warning">
            <i class="fa fa-sign-in"></i> Tap button below to <strong>Check In</strong>
          </div>
          {{ Form::open(['route' => ['parking_tickets.checkin', $parking_ticket->ticket_number], 'method' => 'POST']) }}
            <button type="submit" class="btn btn-success action-btn btn-block">
              <i class="fa fa-sign-in fa-2x"></i><br>
              <strong>CHECK IN</strong>
            </button>
          {{ Form::close() }}

        @elseif($parking_ticket->status == 'checked_in')
          <div class="alert alert-info">
            <i class="fa fa-sign-out"></i> Tap button below to <strong>Check Out</strong>
          </div>
          {{ Form::open(['route' => ['parking_tickets.checkout', $parking_ticket->ticket_number], 'method' => 'POST']) }}
            <button type="submit" class="btn btn-warning action-btn btn-block">
              <i class="fa fa-sign-out fa-2x"></i><br>
              <strong>CHECK OUT</strong>
            </button>
          {{ Form::close() }}

        @elseif($parking_ticket->status == 'checked_out')
          <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> <strong>Parking Completed!</strong>
          </div>
          <a href="{{ route('parking_tickets.receipt', $parking_ticket->ticket_number) }}" class="btn btn-primary action-btn btn-block mb-2">
            <i class="fa fa-file-text"></i> View Receipt
          </a>
        @endif
      </div>

      <div class="text-center mt-4">
        {{ link_to_route('parking_tickets.index', 'Back to List', [], ['class' => 'btn btn-primary']) }}
      </div>
    </div>
  </div>
</div>
@endsection
