<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Group Ticket Print</title>
  <style>
    /* Watermark Layer */
    .watermark-layer {
    position: absolute;
    bottom: 80px;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    pointer-events: none;
    background-image: url(http://localhost/safina-update/public/img/watermark.png);
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 70% auto;
    opacity: 0.8;
}

    @media print {
      body {
        width: 3in;
        margin: 0;
        padding: 0;
      }
      .ticket {
        page-break-after: always;
        width: 3in;
        height: 5in;
        border: 2px solid #000;
        padding: 15px;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
        position: relative;
        z-index: 1;
      }
      .ticket:last-child {
        page-break-after: auto;
      }
      .watermark-layer {
        opacity: 0.8 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        visibility: visible !important;
      }
      /* Make content transparent to show watermark */
      body, .ticket {
        background: transparent !important;
        background-color: transparent !important;
      }
      .print-button {
        display: none;
      }
    }
    body {
      width: 3in;
      margin: 0 auto;
      padding: 10px;
    }
    .ticket {
      width: 3in;
      min-height: 5in;
      border: 2px solid #333;
      padding: 15px;
      margin-bottom: 20px;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
      background: #fff;
      position: relative;
      z-index: 1;
    }
    .ticket-header {
      text-align: center;
      border-bottom: 2px solid #333;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .ticket-title {
      font-size: 18px;
      font-weight: bold;
      margin: 0;
    }
    .ticket-info {
      margin: 10px 0;
    }
    .ticket-info-row {
      display: flex;
      justify-content: space-between;
      margin: 8px 0;
      font-size: 14px;
    }
    .ticket-info-label {
      font-weight: bold;
    }
    .ticket-number {
      text-align: center;
      font-size: 12px;
      font-family: monospace;
      margin: 10px 0;
      padding: 5px;
      background: #f0f0f0;
      border: 1px solid #ccc;
    }
    .qr-code {
      text-align: center;
      margin: 15px 0;
    }
    .qr-code img {
      width: 150px;
      height: 150px;
    }
    .ticket-footer {
      text-align: center;
      font-size: 11px;
      color: #666;
      margin-top: 15px;
      border-top: 1px solid #ccc;
      padding-top: 10px;
    }
    .discount-row {
      color: #d9534f;
    }
    .final-price {
      font-size: 16px;
      font-weight: bold;
      color: #5cb85c;
    }
    .print-button {
      text-align: center;
      margin: 20px 0;
    }
    .btn-print {
      background: #5cb85c;
      color: white;
      padding: 10px 30px;
      font-size: 16px;
      border: none;
      cursor: pointer;
    }
    .group-info {
      text-align: center;
      margin-bottom: 20px;
      padding: 10px;
      background: #f9f9f9;
      border: 1px solid #ddd;
    }
    .group-info h3 {
      margin: 0 0 5px 0;
      font-size: 16px;
    }
    .group-info p {
      margin: 0;
      font-size: 14px;
      color: #666;
    }
  </style>
</head>
<body>

<div class="print-button">
  <button class="btn-print" onclick="window.print()">Print All Tickets</button>
  <br>
  <p><a href="{{ route('ticket_sales.index') }}" class="btn-print" style="background: #337ab7; text-decoration: none; margin-left: 10px;">Back to List</a></p>
</div>

<div class="group-info">
  <p>Total Tickets: {{ $tickets->count() }}</p>
</div>

@foreach ($tickets as $ticket_sale)
<div class="ticket">
  <div class="ticket-header">
    <h2 class="ticket-title">{{ $ticket_sale->ticket ? $ticket_sale->ticket->name : 'TICKET' }}</h2>
  </div>

  <div class="ticket-number text-center">
     <strong>TICKET</strong>
  </div>
<!-- Watermark Layer -->
<div class="watermark-layer"></div>

  <div class="ticket-info">
    <div class="ticket-info-row">
      <span class="ticket-info-label">Price:</span>
      <span>{{ number_format($ticket_sale->price, 2) }} Tk</span>
    </div>

    @if($ticket_sale->discount_amount > 0)
    <div class="ticket-info-row discount-row">
      <span class="ticket-info-label">Discount:</span>
      <span>-{{ number_format($ticket_sale->discount_amount, 2) }} Tk</span>
    </div>
    @endif

    <div class="ticket-info-row final-price">
      <span class="ticket-info-label">Total Price:</span>
      <span>{{ number_format($ticket_sale->total_price, 2) }} Tk</span>
    </div>

    <div class="ticket-info-row">
      <span class="ticket-info-label">Date:</span>
      <span>{{ $ticket_sale->date ? $ticket_sale->date->format('d-m-Y') : date('d-m-Y') }}</span>
    </div>

    <div class="ticket-info-row">
      <span class="ticket-info-label">Time:</span>
      <span>{{ $ticket_sale->created_at ? $ticket_sale->created_at->format('h:i A') : date('h:i A') }}</span>
    </div>

  </div>

  <div class="qr-code">
    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate(url('/ticket/verify/' . $ticket_sale->qr_code)) !!}
  </div>

  <div class="ticket-footer">
    <p>Present this ticket at the entrance</p>
    <p>Valid for single entry only</p>
    <br>
    <p>Thank you for choosing Safina Park & Resort!</p>
  </div>
</div>
@endforeach

<script>
  // Auto print after page load (optional)
  // window.onload = function() { window.print(); }
</script>

</body>
</html>
