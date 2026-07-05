@extends('layouts.admin')
@section('title', 'Extra Payment - ' . $parking_ticket->ticket_number)
@section('content')
<style>
  .payment-card {
    max-width: 600px;
    margin: 0 auto;
  }
  .alert-due {
    background: #fff3cd;
    border: 2px solid #ffc107;
    color: #856404;
  }
  .amount-highlight {
    font-size: 2rem;
    font-weight: bold;
  }
  .breakdown-table {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
  }
  .breakdown-table td {
    padding: 8px 0;
  }
  .breakdown-table td:first-child {
    font-weight: 600;
    width: 50%;
  }
  .total-row {
    border-top: 2px solid #333;
    font-weight: bold;
  }
  .extra-row {
    background: #fff3cd;
  }
</style>

<div class="payment-card">
  <div class="card">
    <div class="card-header bg-warning text-dark">
      <h4 class="mb-0"><i class="fa fa-exclamation-triangle"></i> Extra Payment Required</h4>
      <p class="mb-0">{{ $parking_ticket->ticket_number }}</p>
    </div>
    <div class="card-body">
      <div class="alert alert-due mb-4">
        <div class="text-center">
          <p class="mb-2"><i class="fa fa-clock-o fa-2x"></i></p>
          <h5>Vehicle Overstayed</h5>
          <p class="mb-2">Additional slot charges apply</p>
          <div class="amount-highlight text-warning">
            {{ number_format($extra_amount, 2) }} Tk Due
          </div>
        </div>
      </div>

      <div class="breakdown-table mb-4">
        <table class="table table-borderless mb-0">
          <tr>
            <td>Vehicle Type:</td>
            <td>{{ $parking_ticket->vehicle->name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <td>Vehicle Number:</td>
            <td><strong>{{ $parking_ticket->vehicle_number }}</strong></td>
          </tr>
          <tr>
            <td>Entry Time:</td>
            <td>{{ $parking_ticket->entry_time->format('d-m-Y H:i:s') }}</td>
          </tr>
          <tr>
            <td>Exit Time:</td>
            <td>{{ now()->format('d-m-Y H:i:s') }}</td>
          </tr>
          <tr>
            <td>Total Time:</td>
            <td>{{ $total_hours }} hours ({{ $total_minutes }} min)</td>
          </tr>
          <tr>
            <td>Slot Duration:</td>
            <td>{{ substr($parking_ticket->parking_slot_start_time, 0, 5) }} - {{ substr($parking_ticket->parking_slot_end_time, 0, 5) }}</td>
          </tr>
          <tr>
            <td>Slot Price:</td>
            <td>{{ number_format($parking_ticket->base_price, 2) }} Tk</td>
          </tr>
          <tr>
            <td>Slots Used:</td>
            <td><strong>{{ $slot_multiplier }} slot(s)</strong></td>
          </tr>
          <tr class="table-light">
            <td>Already Paid:</td>
            <td>{{ number_format($parking_ticket->paid_amount ?? $parking_ticket->base_price, 2) }} Tk</td>
          </tr>
          <tr class="extra-row">
            <td>Extra Amount:</td>
            <td><strong class="text-warning">{{ number_format($extra_amount, 2) }} Tk</strong></td>
          </tr>
          <tr class="total-row">
            <td>Total Amount:</td>
            <td><strong>{{ number_format($total_amount, 2) }} Tk</strong></td>
          </tr>
        </table>
      </div>

      <div class="text-center">
        {{ Form::open(['route' => ['parking_tickets.process_extra_payment', $parking_ticket->ticket_number], 'method' => 'POST']) }}
          {{ Form::hidden('slot_multiplier', $slot_multiplier) }}
          {{ Form::hidden('total_amount', $total_amount) }}
          {{ Form::hidden('extra_amount', $extra_amount) }}
          {{ Form::hidden('total_minutes', $total_minutes) }}
          {{ Form::hidden('total_hours', $total_hours) }}

          <button type="submit" class="btn btn-warning btn-lg btn-block">
            <i class="fa fa-money fa-2x"></i><br>
            <strong>COLLECT {{ number_format($extra_amount, 2) }} TK</strong><br>
            <small>And Complete Checkout</small>
          </button>
        {{ Form::close() }}

        <a href="{{ route('parking_tickets.show', $parking_ticket->ticket_number) }}" class="btn btn-default btn-lg mt-3">
          Cancel & Back to Ticket
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
