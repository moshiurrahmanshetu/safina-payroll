@extends('layouts.admin')
@section('title', 'Entry Receipt - ' . $parking_ticket->ticket_number)
@section('content')
<style>
  @media print {
    .no-print { display: none !important; }
    .sidebar, .navbar, .breadcrumb, .page-header { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; background: transparent !important; }
    body { background: white !important; font-size: 12px; }
    .watermark-layer {
      display: block;
      opacity: 0.06;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
  }
  .receipt-card {
    max-width: 320px;
    margin: 0 auto;
    border: 1px solid #333;
    padding: 12px;
    background: #fff;
    font-size: 12px;
    line-height: 1.3;
    position: relative;
  }
  .receipt-header {
    text-align: center;
    border-bottom: 1px dashed #333;
    padding-bottom: 8px;
    margin-bottom: 10px;
  }
  .receipt-title {
    font-size: 14px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
  }
  .receipt-header p {
    margin: 2px 0;
    font-size: 11px;
  }
  .receipt-number {
    font-size: 13px;
    color: #e74c3c;
    font-weight: bold;
  }
  .receipt-table {
    width: 100%;
    margin-bottom: 10px;
    font-size: 11px;
  }
  .receipt-table td {
    padding: 3px 0;
    border-bottom: 1px dotted #ddd;
    vertical-align: top;
  }
  .receipt-table td:first-child {
    font-weight: 600;
    width: 35%;
    color: #555;
  }
  .amount-box {
    background: #ffffff4d;
    border: 1px solid #28a745;
    padding: 8px;
    z-index: 2;
    text-align: center;
    margin: 10px 0;
    display: block;
    position: relative;
  }
  .amount-label {
    font-size: 10px;
    color: #6c757d;
    text-transform: uppercase;
  }
  .amount-value {
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
    line-height: 1.2;
  }
  .stamp-box {
    border: 2px solid #28a745;
    color: #28a745;
    font-size: 21px;
    font-weight: bold;
    text-align: center;
    padding: 6px 22px;
    margin: 8px 0;
    transform: rotate(-3deg);
    display: inline-block;
    position: relative;
    z-index: 2;
  }
  .qr-section {
    text-align: center;
    margin: 8px 0;
  }
  .qr-code-box {
    background: white;
    padding: 5px;
    display: inline-block;
    border: 1px solid #ddd;
  }
  .qr-text {
    font-size: 9px;
    color: #666;
    margin-top: 2px;
  }
  .footer-note {
    text-align: center;
    font-size: 9px;
    color: #6c757d;
    margin-top: 8px;
    border-top: 1px solid #ddd;
    padding-top: 8px;
    line-height: 1.4;
  }
  .footer-note p {
    margin: 2px 0;
  }
  .footer-note hr {
    margin: 5px 0;
    border-top: 1px dotted #ccc;
  }
   .watermark-layer {
    position: absolute;
    bottom: 80px;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    pointer-events: none;
    background-image: url(http://localhost/safina-update/public/img/watermark.png);
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 70% auto;
    opacity: 0.8;
    display: block;
}
@media print {
      .watermark-layer {
        opacity: 0.8 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        visibility: visible !important;
      }
    }
</style>

<div class="row">
  <div class="col-md-12">
    <div class="receipt-card" id="receipt-card">
      <div class="receipt-header">
        <h3 class="receipt-title">SAFINA PARK & RESORT</h3>
        <p class="mb-0 text-muted">Parking Entry Receipt</p>
        <!-- <p class="receipt-number mt-2">{{ $parking_ticket->ticket_number }}</p> -->
      </div>

      <table class="receipt-table">
        <tr>
          <td>Type:</td>
          <td>{{ $parking_ticket->vehicle->name ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td>Vehicle No:</td>
          <td><strong>{{ $parking_ticket->vehicle_number }}</strong></td>
        </tr>
        <tr>
          <td>Driver:</td>
          <td>
            {{ $parking_ticket->driver_name ?? 'N/A' }}
            @if($parking_ticket->driver_phone)
              <br><small>{{ $parking_ticket->driver_phone }}</small>
            @endif
          </td>
        </tr>
        <tr>
          <td>Entry:</td>
          <td>{{ $parking_ticket->entry_time->format('d-m-Y H:i') }}</td>
        </tr>
        <tr>
          <td>Slot:</td>
          <td>{{ substr($parking_ticket->parking_slot_start_time, 0, 5) }} - {{ substr($parking_ticket->parking_slot_end_time, 0, 5) }}</td>
        </tr>
        <tr>
          <td>Price:</td>
          <td>{{ number_format($parking_ticket->base_price, 2) }} Tk</td>
        </tr>
      </table>
<div class="watermark-layer"></div>


      <div class="amount-box">
        <div class="amount-value">{{ number_format($parking_ticket->paid_amount, 2) }} Tk</div>
      </div>

      <div class="text-center">
        <div class="stamp-box">PAID</div>
      </div>

      <div class="qr-section">
        <div class="qr-code-box">
          {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate(route('parking_tickets.scan', $parking_ticket->ticket_number)) !!}
        </div>
        <p class="qr-text">
          <strong>Scan at exit</strong><br>
          Thank you for parking with us!
        </p>
      </div>

      <div class="footer-note">
        <p class="mb-0"><small>By: {{ $parking_ticket->creator->name ?? 'N/A' }} | {{ now()->format('d-m-Y H:i') }}</small></p>
      </div>
    </div>

    <div class="text-center mt-4 no-print">
      <button type="button" class="btn btn-primary btn-lg" onclick="printReceipt()">
        <i class="fa fa-print"></i> Print Entry Receipt
      </button>
      <a href="{{ route('parking_tickets.show', $parking_ticket->ticket_number) }}" class="btn btn-success btn-lg ml-2">
        <i class="fa fa-eye"></i> View Ticket
      </a>
      <a href="{{ route('parking_tickets.index') }}" class="btn btn-warning btn-lg ml-2">
        Back to List
      </a>
    </div>
  </div>
</div>

<script>
  function printReceipt() {
    window.print();
  }
  // Auto-print after 1 second
  setTimeout(function() {
    window.print();
  }, 1000);
</script>
@endsection
