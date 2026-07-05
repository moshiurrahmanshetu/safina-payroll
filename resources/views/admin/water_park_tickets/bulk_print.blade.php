@extends('layouts.admin')
@section('title', 'Print Water Park Tickets')
@section('content')

<!-- Print Header -->
<div class="content-header no-print">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-print mr-2"></i>Print Tickets ({{ count($tickets) }})</h1>
      </div>
      <div class="col-sm-6">
        <div class="float-right">
          <button onclick="window.print()" class="btn btn-primary mr-2">
            <i class="fa fa-print mr-1"></i> Print Now
          </button>
          <a href="{{ route('water_park_tickets.index') }}" class="btn btn-success">
            <i class="fa fa-list mr-1"></i> Back to List
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Print Styles -->
<style>
  @media print {
    /* Hide everything not for printing */
    .no-print,
    .main-header,
    .sidebar,
    .main-footer,
    .content-header,
    .btn {
      display: none !important;
    }
    
    /* Page settings */
    body {
      background: white !important;
      margin: 0 !important;
      padding: 0 !important;
    }
    
    .content-wrapper {
      margin-left: 0 !important;
      padding: 0 !important;
    }
    
    .container-fluid {
      padding: 0 !important;
    }
    
    /* Ticket card for print */
    .ticket-container {
      page-break-after: always;
      padding: 20px;
    }
    
    .ticket-container:last-child {
      page-break-after: auto;
    }
    
    .ticket-receipt {
      border: 2px solid #333 !important;
      padding: 15px;
      max-width: 320px;
      margin: 0 auto;
    }
    
    /* QR code print size */
    .qr-print {
      width: 90px !important;
      height: 90px !important;
    }
  }
  
  /* Screen preview styles */
  .ticket-container {
    margin-bottom: 30px;
  }
  
  .ticket-receipt {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    max-width: 360px;
    margin: 0 auto;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    position: relative;
  }
  
  .ticket-receipt-header {
    text-align: center;
    border-bottom: 2px dashed #dee2e6;
    padding-bottom: 15px;
    margin-bottom: 15px;
  }
  
  .ticket-receipt-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 5px;
  }
  
  .ticket-receipt-subtitle {
    font-size: 14px;
    color: #6c757d;
  }
  
  .ticket-info-table {
    width: 100%;
    margin-bottom: 15px;
    position: relative;
    z-index: 2;
  }
  
  .ticket-info-table th {
    text-align: left;
    padding: 8px 0;
    font-weight: 600;
    width: 40%;
  }
  
  .ticket-info-table td {
    padding: 8px 0;
    text-align: right;
  }
  
  .ticket-amount-box {
    background: #e8f5e94a;
    border: 2px solid #4caf50;
    border-radius: 6px;
    padding: 15px;
    text-align: center;
    margin: 15px 0;
    position: relative;
    z-index: 2;
  }
  
  .ticket-amount-label {
    font-size: 12px;
    font-weight: 700;
    color: #666;
    margin-bottom: 5px;
  }
  
  .ticket-amount-value {
    font-size: 24px;
    font-weight: bold;
    color: #2e7d32;
  }
  
  .ticket-qr-section {
    text-align: center;
    padding: 15px 0;
    border-top: 2px dashed #dee2e6;
    border-bottom: 2px dashed #dee2e6;
  }
  
  .ticket-qr-section canvas,
  .ticket-qr-section img {
    display: block;
    margin: 0 auto 10px;
  }
  
  .ticket-qr-text {
    font-size: 11px;
    color: #6c757d;
    font-family: monospace;
  }
  
  .ticket-footer {
    text-align: center;
    margin-top: 15px;
    font-size: 11px;
    color: #6c757d;
  }
  
  .ticket-divider {
    border-top: 3px dashed #333;
    margin: 20px -20px;
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
</style>

<!-- Tickets Grid -->
<div class="container-fluid">
  @forelse($tickets as $index => $ticket)
  <div class="ticket-container">
    <div class="ticket-receipt">
      <!-- Header -->
      <div class="ticket-receipt-header">
        <div class="ticket-receipt-title">SAFINA PARK & RESORT</div>
        <div class="ticket-receipt-subtitle">Water Park Entry Ticket</div>
      </div>
      
      <!-- Ticket Info -->
      <table class="ticket-info-table">
        <tr>
          <th>Counter:</th>
          <td>{{ $ticket->waterParkCounter->name ?? 'N/A' }}</td>
        </tr>
        <tr>
          <th>Duration:</th>
          <td>{{ $ticket->duration_minutes }} min ({{ number_format($ticket->duration_minutes / 60, 1) }} hrs)</td>
        </tr>
        <tr>
          <th>Created:</th>
          <td>{{ $ticket->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
          <th>By:</th>
          <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
        </tr>
      </table>
<div class="watermark-layer"></div>
      
      <!-- Price Box -->
      <div class="ticket-amount-box">
        <div class="ticket-amount-label">ENTRY FEE</div>
        <div class="ticket-amount-value">{{ number_format($ticket->price, 2) }} Tk</div>
      </div>
      
      <!-- QR Code -->
      <div class="ticket-qr-section">
        {!! QrCode::size(90)->generate(route('water_park_tickets.scan', $ticket->ticket_number)) !!}
        <div class="ticket-qr-text mt-2">SCAN TO CHECK-IN/OUT</div>
      </div>
      
      <!-- Footer -->
      <div class="ticket-footer">
        <div>Keep this ticket for entry and exit</div>
        <div>No refund | No exchange</div>
      </div>
    </div>
    
    @if(!$loop->last)
    <div class="ticket-divider no-print"></div>
    @endif
  </div>
  @empty
  <div class="text-center text-muted py-5">
    <i class="fa fa-inbox fa-3x mb-3"></i>
    <h4>No tickets to print</h4>
  </div>
  @endforelse
</div>

<!-- Auto Print Script -->
<script>
  setTimeout(function() {
    window.print();
  }, 1000);
</script>

@endsection
