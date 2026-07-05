@extends('layouts.admin')
@section('title', 'Water Park Ticket - ' . $ticket->ticket_number)
@section('content')
<style>
  @media print {
    .no-print { display: none !important; }
    .sidebar, .navbar, .breadcrumb, .page-header { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; background: transparent !important; }
    body { background: white !important; font-size: 12px; }
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
  .amount-value {
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
    line-height: 1.2;
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
</style>

<!-- Page Header -->
<div class="content-header no-print">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-ticket-alt mr-2"></i>Ticket Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('water_park_tickets.index') }}">Water Park Tickets</a></li>
          <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Success Message -->
@if(session('flash_success'))
<div class="alert alert-success no-print">
  <i class="fa fa-check-circle mr-2"></i>{{ session('flash_success') }}
</div>
@endif

<!-- Ticket Receipt -->
<div class="row">
  <div class="col-md-12">
    <div class="receipt-card" id="receipt-card">
      <div class="receipt-header">
        <h3 class="receipt-title">SAFINA PARK & RESORT</h3>
        <p class="mb-0 text-muted">Water Park Entry Ticket</p>
      </div>

      <table class="receipt-table">
        <tr>
          <td>Ticket #:</td>
          <td><strong>{{ $ticket->ticket_number }}</strong></td>
        </tr>
        <tr>
          <td>Counter:</td>
          <td>{{ $ticket->waterParkCounter->name ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td>Duration:</td>
          <td>{{ $ticket->duration_minutes }} Minutes ({{ number_format($ticket->duration_minutes / 60, 1) }} Hours)</td>
        </tr>
        <tr>
          <td>Price:</td>
          <td>{{ number_format($ticket->price, 2) }} Tk</td>
        </tr>
      </table>

      <div class="amount-box">
        <div class="amount-value">{{ number_format($ticket->price, 2) }} Tk</div>
      </div>

      <div class="qr-section">
        <div class="qr-code-box">
          {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate('WP-' . $ticket->id) !!}
        </div>
        <p class="qr-text">
          <strong>Scan for Check-in/Out</strong><br>
          Keep this ticket safe!
        </p>
      </div>

      <div class="footer-note">
        <p class="mb-0"><small>By: {{ $ticket->creator->name ?? 'N/A' }} | {{ $ticket->created_at->format('d-m-Y H:i') }}</small></p>
      </div>
    </div>

    <div class="text-center mt-4 no-print">
      <button type="button" class="btn btn-primary btn-lg" onclick="printTicket()">
        <i class="fa fa-print"></i> Print Ticket
      </button>
      <a href="{{ route('water_park_tickets.show', $ticket->id) }}" class="btn btn-success btn-lg ml-2">
        <i class="fa fa-eye"></i> View Ticket
      </a>
      <a href="{{ route('water_park_tickets.index') }}" class="btn btn-warning btn-lg ml-2">
        Back to List
      </a>
      <a href="{{ route('water_park_tickets.create') }}" class="btn btn-info btn-lg ml-2">
        <i class="fa fa-plus"></i> Create Another
      </a>
    </div>
  </div>
</div>

<script>
  function printTicket() {
    window.print();
  }
  // Auto-print after 1 second
  setTimeout(function() {
    window.print();
  }, 1000);
</script>

@endsection
