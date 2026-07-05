<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Package Ticket Print</title>
  <style>
    /* Watermark Layer */
    .watermark-layer {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      pointer-events: none;
      background-image: url('{{ asset('public/img/watermark.png') }}');
      background-repeat: no-repeat;
      background-position: center center;
      background-size: 60% auto;
      opacity: 0.03;
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
        height: 6in;
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
    }
    body {
      width: 3in;
      margin: 0 auto;
      padding: 10px;
    }
    .ticket {
      width: 3in;
      min-height: 6in;
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
    .package-name {
      font-size: 14px;
      color: #666;
      margin-top: 5px;
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
    .entry-badge {
      background: #5cb85c;
      color: white;
      padding: 3px 10px;
      border-radius: 3px;
      font-size: 11px;
      display: inline-block;
      margin-bottom: 10px;
    }
    @media print {
      .print-button {
        display: none;
      }
    }
  </style>
</head>
<body>


<div class="print-button">
  <button class="btn-print" onclick="window.print()">Print Package Tickets</button>
  <br><br>
  <a href="{{ route('package_bookings.index') }}" class="btn btn-primary">Back to Bookings</a>
</div>

@foreach ($tickets as $ticket)
<div class="ticket">
  <div class="ticket-header">
    <h2 class="ticket-title">{{ $ticket['ticket_name'] }}</h2>
    <div class="package-name">{{ $booking->package ? $booking->package->name : 'Package' }}</div>
    @if(stripos($ticket['ticket_name'], 'entry') !== false)
    <span class="entry-badge">ENTRY TICKET</span>
    @endif
  </div>
<!-- Watermark Layer -->
<div class="watermark-layer"></div>

  <div class="ticket-info">
    <div class="ticket-info-row">
      <span class="ticket-info-label">Date:</span>
      <span>{{ date('d-m-Y', strtotime($booking->date)) }}</span>
    </div>

    <div class="ticket-info-row">
      <span class="ticket-info-label">Time:</span>
      <span>{{ date('h:i A', strtotime($booking->created_at)) }}</span>
    </div>

    <div class="ticket-info-row">
      <span class="ticket-info-label">Counter:</span>
      <span>{{ $booking->packageCounter ? $booking->packageCounter->name : 'Main Gate' }}</span>
    </div>

    <div class="ticket-info-row">
      <span class="ticket-info-label">Total Person:</span>
      <span>{{ $booking->total_person }}</span>
    </div>
  </div>

  @if($ticket['ticket_token'])
  <div class="qr-code">
    {!! QrCode::size(120)->generate(route('package.scan.token', ['package_token' => $booking->booking_token, 'ticket_token' => $ticket['ticket_token']])) !!}
  </div>
  @endif

  <div class="ticket-footer">
    <p>Valid for {{ $booking->total_person }} person(s)</p>
    <p>Booking Ref: {{ $booking->id }}</p>
    <p>Scan QR for entry</p>
  </div>
</div>
@endforeach

<script>
  // Auto print after page load
  window.onload = function() {
    setTimeout(function() {
      window.print();
    }, 500);
  };
</script>

</body>
</html>
