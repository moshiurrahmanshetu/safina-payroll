@extends('layouts.admin')
@section('title', 'Locker & Gear Ticket')
@section('content')

<!-- Print Styles -->
<style>
  @media print {
    .no-print, .main-header, .sidebar, .main-footer { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    body { background: white !important; }
  }
  .ticket-receipt {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    max-width: 360px;
    margin: 0 auto;
    background: white;
  }
  .ticket-header {
    text-align: center;
    border-bottom: 2px dashed #dee2e6;
    padding-bottom: 15px;
    margin-bottom: 15px;
  }
  .ticket-title { font-size: 18px; font-weight: bold; }
  .ticket-subtitle { font-size: 12px; color: #6c757d; }
  .ticket-info th { text-align: left; width: 40%; padding: 8px 0; }
  .ticket-info td { text-align: right; padding: 8px 0; }
  .amount-box {
    background: #e8f5e9;
    border: 2px solid #4caf50;
    border-radius: 6px;
    padding: 15px;
    text-align: center;
    margin: 15px 0;
  }
  .qr-section {
    text-align: center;
    padding: 15px 0;
    border-top: 2px dashed #dee2e6;
  }
</style>

<!-- Header -->
<div class="content-header no-print">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        
      </div>
      <div class="col-sm-6">
        <div class="float-right">
          <button onclick="window.print()" class="btn btn-primary mr-2">
            <i class="fa fa-print mr-1"></i> Print
          </button>
          <a href="{{ route('locker_gear_tickets.index') }}" class="btn btn-success">
            <i class="fa fa-list mr-1"></i> Back to List
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Receipt -->
<div class="container-fluid">
  <div class="ticket-receipt">
    <div class="ticket-header">
      <div class="ticket-title">SAFINA PARK & RESORT</div>
      <div class="ticket-subtitle">Locker & Gear Rental</div>
    </div>

    <table class="ticket-info w-100">
      <tr>
        <th>Status:</th>
        <td>
          @if($ticket->status == 'checked_in')
            <span class="badge badge-success">Checked In</span>
          @else
            <span class="badge badge-secondary">Checked Out</span>
          @endif
        </td>
      </tr>
      <tr>
        <th>Entry:</th>
        <td>{{ $ticket->entry_time ? $ticket->entry_time->format('d M Y, h:i A') : 'N/A' }}</td>
      </tr>
      @if($ticket->exit_time)
      <tr>
        <th>Exit:</th>
        <td>{{ $ticket->exit_time->format('d M Y, h:i A') }}</td>
      </tr>
      @endif
    </table>

    <!-- Items -->
    <h6 class="font-weight-bold mb-2"><i class="fa fa-box mr-1"></i>Rented Items:</h6>
    <table class="table table-sm table-bordered">
      @foreach($ticket->items as $item)
      <tr>
        <td>
          @if($item->item_type == 'locker')
            <i class="fa fa-lock mr-1 text-info"></i> Locker
          @else
            <i class="fa fa-tshirt mr-1 text-warning"></i> Gear
          @endif
        </td>
        <td><strong>{{ $item->item->name ?? 'N/A' }}</strong></td>
        <td class="text-center">x{{ $item->quantity }}</td>
      </tr>
      @endforeach
    </table>

    <!-- Billing Breakdown -->
    <h6 class="font-weight-bold mb-2"><i class="fa fa-receipt mr-1"></i>Billing:</h6>
    <table class="table table-sm">
      <tr>
        <td>Base Price:</td>
        <td class="text-right">{{ number_format($ticket->total_amount - $ticket->extra_amount, 2) }} Tk</td>
      </tr>
      @if($ticket->extra_amount > 0)
      <tr>
        <td>Extra Charge:</td>
        <td class="text-right text-danger">+{{ number_format($ticket->extra_amount, 2) }} Tk</td>
      </tr>
      @endif
    </table>

    <!-- Amount -->
    <div class="amount-box">
      <div style="font-size: 12px; color: #666;">TOTAL AMOUNT</div>
      <div style="font-size: 24px; font-weight: bold; color: #2e7d32;">
        {{ number_format($ticket->total_amount, 2) }} Tk
      </div>
    </div>

    <!-- QR -->
    <div class="qr-section">
      {!! QrCode::size(90)->generate(route('locker_gear_tickets.scan', $ticket->ticket_number)) !!}
      <div class="mt-2" style="font-size: 11px; color: #6c757d; font-family: monospace;">
        SCAN FOR CHECK-OUT
      </div>
    </div>

    <div class="text-center mt-3" style="font-size: 11px; color: #6c757d;">
      <div>Keep this ticket for return</div>
      <div>Created by: {{ $ticket->creator->name ?? 'N/A' }}</div>
    </div>
  </div>
</div>

<!-- Auto Print -->
<script>
  setTimeout(function() {
    window.print();
  }, 1000);
</script>

@endsection
