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
      max-width: 450px;
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
    .header.info {
      background: #17a2b8;
    }
    .header h1 {
      font-size: 24px;
      margin-bottom: 10px;
    }
    .header .icon {
      font-size: 50px;
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
      padding: 12px 0;
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
      font-size: 18px;
      word-break: break-all;
      border: 2px solid #ddd;
    }
    .timestamp {
      text-align: center;
      color: #666;
      font-size: 14px;
      margin-top: 20px;
    }
    .buttons {
      display: flex;
      gap: 10px;
      margin-top: 25px;
    }
    .btn {
      flex: 1;
      padding: 15px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      text-decoration: none;
      text-align: center;
      font-weight: bold;
    }
    .btn-verify {
      background: #28a745;
      color: white;
    }
    .btn-verify:hover {
      background: #218838;
    }
    .btn-back {
      background: #6c757d;
      color: white;
    }
    .btn-back:hover {
      background: #5a6268;
    }
    .btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
    .status-badge {
      display: inline-block;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: bold;
      margin-top: 10px;
    }
    .status-valid {
      background: #d4edda;
      color: #155724;
    }
    .status-invalid {
      background: #f8d7da;
      color: #721c24;
    }
    .status-used {
      background: #fff3cd;
      color: #856404;
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
        @if($status == 'preview')
          &#128269;
        @elseif($status == 'valid')
          &#10004;
        @elseif($status == 'invalid')
          &#10008;
        @else
          &#9888;
        @endif
      </div>
      <h1>{{ $message }}</h1>
      @if($status == 'preview')
        <span class="status-badge status-valid">Ready to Verify</span>
      @elseif($status == 'used')
        <span class="status-badge status-used">Already Used</span>
      @elseif($status == 'expired')
        <span class="status-badge status-invalid">Expired</span>
      @elseif($status == 'invalid')
        <span class="status-badge status-invalid">Not Found</span>
      @endif
    </div>

    <div class="content">
      @if(isset($ticket))
        <div class="ticket-number">
          {{ $ticket->qr_code }}
        </div>

        <div class="ticket-info">
          <div class="ticket-info-row">
            <span class="label">Ticket Type:</span>
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
          <div class="ticket-info-row">
            <span class="label">Sale Time:</span>
            <span class="value">{{ date('h:i A', strtotime($ticket->created_at)) }}</span>
          </div>
          @if($status == 'used' && isset($used_at))
          <div class="ticket-info-row">
            <span class="label">Used At:</span>
            <span class="value" style="color: #dc3545; font-weight: bold;">
              {{ date('d-m-Y h:i A', strtotime($used_at)) }}
            </span>
          </div>
          @endif
          @if($ticket->gate)
          <div class="ticket-info-row">
            <span class="label">Gate:</span>
            <span class="value">{{ $ticket->gate->name }}</span>
          </div>
          @endif
        </div>
      @endif

      <div class="timestamp">
        Previewed: {{ date('d-m-Y h:i:s A') }}
      </div>

      <div class="buttons">
        @if($status == 'preview')
          <form method="POST" action="{{ route('ticket.verify.confirm') }}" style="flex: 1;">
            @csrf
            <input type="hidden" name="ticket_number" value="{{ $ticket->qr_code }}">
            <button type="submit" class="btn btn-verify">
              &#10004; Verify Ticket
            </button>
          </form>
        @endif
        <a href="{{ route('ticket_sales.scan') }}" class="btn btn-back">
          &#8592; Back to Scan
        </a>
      </div>
    </div>

    <div class="footer">
      Ticket Verification System - Safina Park & Resort
    </div>
  </div>
</body>
</html>
