@extends('layouts.admin')
@section('title', 'Parking Receipt')
@section('content')
<!-- Watermark Layer for Print -->


<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card" id="receipt-card">
      <div class="card-header text-center bg-primary text-white">
        <h4><i class="fa fa-car"></i> Parking Receipt</h4>
        <p class="mb-0">Safina Park & Resort</p>
        <small>Godagari, Rajshahi, Bangladesh</small>
      </div>
      <div class="card-body">
        <div class="text-center mb-4">
          <h3 class="text-success">PAID</h3>
          <p class="lead mb-0"><strong>Ticket: {{ $parking_ticket->ticket_number }}</strong></p>
          <small class="text-muted">{{ $parking_ticket->created_at->format('d-m-Y') }}</small>
        </div>

        <hr>
        <div class="watermark-layer"></div>

        <div class="row">
          <div class="col-md-6">
            <table class="table table-borderless table-sm">
              <tr>
                <td><strong>Vehicle Type:</strong></td>
                <td>
                  <span class="badge badge-info">{{ $parking_ticket->vehicle->name ?? 'N/A' }}</span>
                </td>
              </tr>
              <tr>
                <td><strong>Vehicle Number:</strong></td>
                <td>{{ $parking_ticket->vehicle_number }}</td>
              </tr>
              <tr>
                <td><strong>Driver Name:</strong></td>
                <td>{{ $parking_ticket->driver_name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td><strong>Driver Phone:</strong></td>
                <td>{{ $parking_ticket->driver_phone ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-borderless table-sm">
              <tr>
                <td><strong>Entry Time:</strong></td>
                <td>{{ $parking_ticket->entry_time ? $parking_ticket->entry_time->format('d-m-Y H:i:s') : 'N/A' }}</td>
              </tr>
              <tr>
                <td><strong>Exit Time:</strong></td>
                <td>{{ $parking_ticket->exit_time ? $parking_ticket->exit_time->format('d-m-Y H:i:s') : 'N/A' }}</td>
              </tr>
              <tr>
                <td><strong>Duration:</strong></td>
                <td>
                  @if($parking_ticket->total_hours)
                    {{ $parking_ticket->total_hours }} hours
                    @if($parking_ticket->total_minutes)
                      ({{ $parking_ticket->total_minutes }} min)
                    @endif
                  @else
                    N/A
                  @endif
                </td>
              </tr>
              <tr>
                <td><strong>Slot Price:</strong></td>
                <td>{{ number_format($parking_ticket->base_price ?? $parking_ticket->hourly_rate, 2) }} Tk <small class="text-muted">(08:00 - 18:00)</small></td>
              </tr>
              @if($parking_ticket->slot_multiplier)
              <tr>
                <td><strong>Slots Used:</strong></td>
                <td><strong>{{ $parking_ticket->slot_multiplier }} slot(s)</strong></td>
              </tr>
              @endif
              @if($parking_ticket->paid_amount)
              <tr>
                <td><strong>Paid at Entry:</strong></td>
                <td>{{ number_format($parking_ticket->paid_amount, 2) }} Tk</td>
              </tr>
              @endif
              @if($parking_ticket->extra_amount > 0)
              <tr class="table-warning">
                <td><strong>Extra Payment:</strong></td>
                <td><strong>{{ number_format($parking_ticket->extra_amount, 2) }} Tk</strong></td>
              </tr>
              @endif
            </table>
          </div>
        </div>

        <hr>

        <div class="text-center mb-4">
          <h4 class="text-primary">Total Amount</h4>
          <h2 class="text-success">{{ number_format($parking_ticket->total_amount, 2) }} Tk</h2>
          <small class="text-muted">
            @if($parking_ticket->extra_amount > 0)
              (Includes {{ number_format($parking_ticket->extra_amount, 2) }} Tk overstay charge)
            @else
              (No extra charges)
            @endif
          </small>
          <small class="text-muted">Thank you for using our parking service!</small>
        </div>

        <hr>

        <div class="text-center">
          <p class="text-muted mb-0">
            <small>Issued by: {{ $parking_ticket->creator->name ?? 'N/A' }}</small><br>
            <small>{{ now()->format('d-m-Y H:i:s') }}</small>
          </p>
        </div>
      </div>
      <div class="card-footer text-center no-print">
        <button type="button" class="btn btn-primary" onclick="printReceipt()">
          <i class="fa fa-print"></i> Print Receipt
        </button>
        {{ link_to_route('parking_tickets.index', 'Back to List', [], ['class' => 'btn btn-success']) }}
      </div>
    </div>
  </div>
</div>

<style>
/* Watermark for print */
.watermark-layer {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
  pointer-events: none;
  background-image: url('{{ asset('public/img/watermark.png') }}');
  background-repeat: no-repeat;
  background-position: center center;
  background-size: 60% auto;
  opacity: 0.5;
  display: block;
}

@media print {
  .watermark-layer {
  opacity: 0.2 !important;
  z-index: 1;
  background-size: 80% auto;

}

   table, tr, td, th {
    background: transparent !important;
  }
  table{
    position: relative;
  }
  .no-print { display: none !important; }
  .sidebar, .navbar, .breadcrumb, .page-header { display: none !important; }
  .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
  .card { border: none !important; box-shadow: none !important; background: transparent !important; }
  body { background: transparent !important; }
  
}
#receipt-card {
  max-width: 600px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
  background: transparent !important;
}
</style>

<script>
function printReceipt() {
  window.print();
}
</script>
@endsection
