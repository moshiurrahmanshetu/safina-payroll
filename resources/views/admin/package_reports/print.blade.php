<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Package Booking Report</title>
  <style>
    /* Watermark Layer */
    .watermark-layer {
      position: absolute;
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

    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      margin: 20px;
      position: relative;
      z-index: 1;
    }
    h1,h2 {
      text-align: center;
      margin: 5px;
    }
    h2 {
      font-size: 18px;
      margin-bottom: 5px;
    }
    .meta {
      text-align: center;
      color: #666;
      font-size: 10px;
      margin-bottom: 20px;
    }
    .summary {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      padding: 10px;
    }
    .summary-box {
      text-align: center;
      flex: 1;
    }
    .summary-label {
      font-size: 10px;
      color: #666;
    }
    .summary-value {
      font-size: 16px;
      font-weight: bold;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      margin-bottom: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 6px;
      text-align: left;
    }
    th {
      background-color: #f5f5f5;
      font-weight: bold;
    }
    .text-right {
      text-align: right;
    }
    .text-center {
      text-align: center;
    }
    .filters {
      margin-bottom: 15px;
      padding: 8px;
      background-color: #f9f9f9;
      border: 1px solid #eee;
      font-size: 10px;
    }
    .section-title {
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 10px;
      font-size: 14px;
    }
    @media print {
      body {
        margin: 10px;
        background: transparent !important;
        background-color: transparent !important;
      }
      .no-print { display: none; }
      .watermark-layer {
        opacity: 0.8 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        visibility: visible !important;
      }
      /* Make all content transparent to show watermark */
      body, table, td, th, .summary, .filters {
        background: transparent !important;
        background-color: transparent !important;
      }
    }
  </style>
</head>
<body>
  <!-- Watermark Layer -->
  <div class="watermark-layer"></div>

  <div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px;">
      <i class="nav-icon icon-printer"></i> Print Report
    </button>
    <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; margin-left: 10px;">
      Close
    </button>
  </div>

  <h1>Safina Park & Resort</h1>
  <h2>Package Booking Report</h2>
  <div class="meta">
    Generated: {{ now()->format('d-m-Y H:i:s') }}
  </div>

  @if($fromDate || $toDate || $packageId || $counterId || $userId)
  <div class="filters">
    <strong>Filters:</strong>
    @if($fromDate) From: {{ $fromDate }} @endif
    @if($toDate) To: {{ $toDate }} @endif
    @if($packageId)
      @php
        $pkg = \App\Models\Package::find($packageId);
      @endphp
      | Package: {{ $pkg ? $pkg->name : 'Unknown' }}
    @endif
    @if($counterId)
      @php
        $ctr = \App\Models\Counter::find($counterId);
      @endphp
      | Counter: {{ $ctr ? $ctr->name : 'Unknown' }}
    @endif
    @if($userId)
      @php
        $usr = \App\Models\User::find($userId);
      @endphp
      | User: {{ $usr ? $usr->name : 'Unknown' }}
    @endif
  </div>
  @endif

  <div class="summary">
    <div class="summary-box">
      <div class="summary-label">Total Bookings</div>
      <div class="summary-value">{{ $totalBookings }}</div>
    </div>
    <div class="summary-box">
      <div class="summary-label">Total Revenue</div>
      <div class="summary-value">৳{{ number_format($totalRevenue, 2) }}</div>
    </div>
    <div class="summary-box">
      <div class="summary-label">Average per Booking</div>
      <div class="summary-value">
        ৳{{ $totalBookings > 0 ? number_format($totalRevenue / $totalBookings, 2) : '0.00' }}
      </div>
    </div>
  </div>

  <!-- Package Summary -->
  <div class="section-title">Package-wise Summary</div>
  <table>
    <thead>
      <tr>
        <th>Package</th>
        <th class="text-center">Bookings</th>
        <th class="text-right">Revenue</th>
      </tr>
    </thead>
    <tbody>
      @forelse($packageSummary as $name => $data)
      <tr>
        <td>{{ $name }}</td>
        <td class="text-center">{{ $data['count'] }}</td>
        <td class="text-right">৳{{ number_format($data['revenue'], 2) }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="3" class="text-center">No data</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Counter Summary -->
  <div class="section-title">Counter-wise Summary</div>
  <table>
    <thead>
      <tr>
        <th>Counter</th>
        <th class="text-center">Bookings</th>
        <th class="text-right">Revenue</th>
      </tr>
    </thead>
    <tbody>
      @forelse($counterSummary as $name => $data)
      <tr>
        <td>{{ $name }}</td>
        <td class="text-center">{{ $data['count'] }}</td>
        <td class="text-right">৳{{ number_format($data['revenue'], 2) }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="3" class="text-center">No data</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Booking Details -->
  <div class="section-title">Booking Details</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Package</th>
        <th class="text-center">Qty</th>
        <th class="text-center">Person</th>
        <th class="text-right">Final Amount</th>
        <th>Counter</th>
      </tr>
    </thead>
    <tbody>
      @php $i = 1; @endphp
      @forelse($bookings as $booking)
      <tr>
        <td>{{ $i++ }}</td>
        <td>{{ date('d-m-Y', strtotime($booking->date)) }}</td>
        <td>{{ $booking->package ? $booking->package->name : 'N/A' }}</td>
        <td class="text-center">{{ $booking->quantity }}</td>
        <td class="text-center">{{ $booking->total_person }}</td>
        <td class="text-right">৳{{ number_format($booking->final_amount, 2) }}</td>
        <td>{{ $booking->counter ? $booking->counter->name : '-' }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="7" class="text-center">No bookings found</td>
      </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <th colspan="5" class="text-right">Total:</th>
        <th class="text-right">৳{{ number_format($totalRevenue, 2) }}</th>
        <th></th>
      </tr>
    </tfoot>
  </table>

</body>
</html>
