@extends('layouts.admin')
@section('title', 'Scan Locker & Gear Ticket')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-camera mr-2"></i>Locker & Gear Ticket Scan</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('locker_gear_tickets.index') }}">Tickets</a></li>
          <li class="breadcrumb-item active">Scan</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card card-outline card-primary shadow">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-camera mr-2"></i>QR Code Scanner</h3>
        <div class="card-tools">
          <a href="{{ route('locker_gear_tickets.index') }}" class="btn btn-success btn-sm">
            <i class="fa fa-arrow-left mr-1"></i> Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <!-- Camera Scanner -->
        <div class="text-center mb-4">
          <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
          <div class="mt-3">
            <button id="start-camera" class="btn btn-primary" onclick="startCamera()">
              <i class="fa fa-camera mr-2"></i>Start Camera Scan
            </button>
            <button id="stop-camera" class="btn btn-warning" style="display:none;" onclick="stopCamera()">
              <i class="fa fa-stop mr-2"></i>Stop Camera
            </button>
          </div>
          <p class="text-muted mt-3">
            Point camera at ticket QR code. It will auto-redirect to ticket details.
          </p>
        </div>

        <hr class="my-4">

        <!-- Manual Input -->
        <div class="bg-light p-3 rounded">
          <h5 class="mb-3"><i class="fa fa-keyboard mr-2"></i>Manual Entry</h5>
          <div class="form-group">
            <label>Ticket Number</label>
            <div class="input-group">
              <input type="text" id="manual_ticket_id" class="form-control" placeholder="Enter ticket number (e.g., LG20260629E52F)" autofocus>
              <div class="input-group-append">
                <button type="button" class="btn btn-info" onclick="goToTicket()">
                  <i class="fa fa-search mr-1"></i> View
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
// Manual ticket lookup for Locker & Gear
function goToTicket() {
    var ticketNumber = document.getElementById('manual_ticket_id').value.trim();
    if (ticketNumber) {
        // Check if it's a URL or just a number
        if (ticketNumber.includes('/')) {
            var parts = ticketNumber.split('/');
            ticketNumber = parts[parts.length - 1];
        }
        window.location.href = '{{ route("locker_gear_tickets.scan", "") }}/' + encodeURIComponent(ticketNumber);
    } else {
        alert('Please enter a ticket number');
    }
}

// Enter key handler
document.getElementById('manual_ticket_id').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        goToTicket();
    }
});
</script>
@endsection
