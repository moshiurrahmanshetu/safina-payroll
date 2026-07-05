<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ticket Verification</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    .container {
      width: 100%;
      max-width: 400px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .header {
      padding: 30px;
      text-align: center;
      color: white;
    }
    .header.success {
      background: #28a745;
    }
    .header.danger {
      background: #dc3545;
    }
    .header.warning {
      background: #ffc107;
      color: #333;
    }
    .header h1 {
      font-size: 28px;
      margin-bottom: 10px;
    }
    .header .icon {
      font-size: 60px;
      margin-bottom: 10px;
    }
    .content {
      padding: 30px;
    }
    .ticket-info {
      margin-bottom: 20px;
    }
    .ticket-info-row {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid #eee;
    }
    .ticket-info-row:last-child {
      border-bottom: none;
    }
    .label {
      font-weight: bold;
      color: #666;
    }
    .value {
      color: #333;
    }
    .ticket-number {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 20px;
      font-family: monospace;
      font-size: 16px;
      word-break: break-all;
    }
    .timestamp {
      text-align: center;
      color: #666;
      font-size: 14px;
      margin-top: 20px;
    }
    .footer {
      text-align: center;
      padding: 20px;
      background: #f8f9fa;
      color: #666;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header {{ $color }}">
      <div class="icon">
        @if($status == 'valid')
          &#10004;
        @elseif($status == 'invalid')
          &#10008;
        @else
          &#9888;
        @endif
      </div>
      <h1>{{ $message }}</h1>
    </div>

    <div class="content">
      @if(isset($ticket))
        <div class="ticket-number">
          {{ $ticket->qr_code }}
        </div>

        <div class="ticket-info">
          <div class="ticket-info-row">
            <span class="label">Ticket:</span>
            <span class="value">{{ $ticket->ticket->name ?? 'N/A' }}</span>
          </div>
          <div class="ticket-info-row">
            <span class="label">Price:</span>
            <span class="value">{{ number_format($ticket->price, 2) }} Tk</span>
          </div>
          <div class="ticket-info-row">
            <span class="label">Sale Date:</span>
            <span class="value">{{ date('d-m-Y', strtotime($ticket->created_at)) }}</span>
          </div>
          @if($status == 'used' && isset($used_at))
          <div class="ticket-info-row">
            <span class="label">Used At:</span>
            <span class="value">{{ date('d-m-Y h:i A', strtotime($used_at)) }}</span>
          </div>
          @endif
        </div>
      @endif

      <div class="timestamp">
        Verified: {{ date('d-m-Y h:i:s A') }}
      </div>
    </div>

    <div class="footer">
      Ticket Verification System
    </div>
  </div>
</body>
</html>
