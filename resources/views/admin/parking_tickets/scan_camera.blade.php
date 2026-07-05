@extends('layouts.admin')
@section('title', 'Parking Ticket Scan')
@section('content')
<h3 class="page-header">
  <i class="nav-icon icon-qr"></i> Parking Ticket Scan
  <a href="{{ route('parking_tickets.index') }}" class="btn btn-success pull-right">
    <i class="nav-icon icon-arrow-left"></i> Back to List
  </a>
</h3>

<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4><i class="nav-icon icon-camera"></i> QR Code Scanner</h4>
      </div>
      <div class="panel-body">
        <!-- Camera Scanner -->
        <div id="camera-section" style="text-align: center; margin-bottom: 20px;">
          <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
          <button id="start-camera" class="btn btn-primary btn-lg" onclick="startCamera()">
            <i class="nav-icon icon-camera"></i> Start Camera Scan
          </button>
          <button id="stop-camera" class="btn btn-warning btn-lg" style="display:none;" onclick="stopCamera()">
            <i class="nav-icon icon-close"></i> Stop Camera
          </button>
          <p class="text-muted" style="margin-top: 10px;">
            Point camera at parking ticket QR code. It will auto-redirect to ticket details.
          </p>
        </div>

        <hr style="margin: 30px 0;">

        <!-- Manual Input (Backup) -->
        <div class="manual-section">
          <h5><i class="nav-icon icon-keyboard"></i> Manual Entry</h5>
          <div class="form-group">
            <label>Ticket ID / Number</label>
            <input type="text" id="manual_ticket_id" class="form-control" placeholder="Enter ticket ID or scan QR code" autofocus>
          </div>
          <button type="button" class="btn btn-info btn-block" onclick="goToTicket()">View Ticket</button>
        </div>

        <div id="result" style="margin-top: 20px;"></div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
var html5QrCode;
var isScanning = false;

// Handle manual entry
function goToTicket() {
  var ticketId = $('#manual_ticket_id').val().trim();
  if (!ticketId) {
    showResult('danger', 'Please enter ticket ID');
    return;
  }
  redirectToTicket(ticketId);
}

// Handle Enter key on manual input
$('#manual_ticket_id').on('keypress', function(e) {
  if (e.which === 13) {
    goToTicket();
  }
});

function redirectToTicket(ticketId) {
  // Extract ID from URL if QR contains full URL
  var urlPattern = /\/parking-tickets\/(\d+)/;
  var match = ticketId.match(urlPattern);
  if (match) {
    ticketId = match[1];
  }

  // Extract ID from PARK-{id} format
  var parkPattern = /PARK-(\d+)/;
  var parkMatch = ticketId.match(parkPattern);
  if (parkMatch) {
    ticketId = parkMatch[1];
  }

  // Show loading
  showResult('info', '<i class="nav-icon icon-spinner icon-spin"></i> Loading ticket...');

  // Redirect to scan page which shows ticket details with checkIn/checkOut
  window.location.href = '{{ url("/parking/scan") }}/' + ticketId;
}

function onScanSuccess(decodedText, decodedResult) {
  if (isScanning) {
    isScanning = false;

    // Extract ticket ID from URL if it's a URL
    var ticketId = decodedText;
    var urlPattern = /\/parking-tickets\/(\d+)/;
    var match = decodedText.match(urlPattern);

    if (match) {
      ticketId = match[1];
    }

    // Extract ID from PARK-{id} format
    var parkPattern = /PARK-(\d+)/;
    var parkMatch = decodedText.match(parkPattern);
    if (parkMatch) {
      ticketId = parkMatch[1];
    }

    // Stop camera
    stopCamera();

    // Show success message
    showResult('success',
      '<strong>QR CODE SCANNED</strong><br>' +
      'Ticket ID: ' + ticketId + '<br>' +
      '<i class="nav-icon icon-spinner icon-spin"></i> Redirecting...'
    );

    // Redirect to ticket scan page
    setTimeout(function() {
      redirectToTicket(ticketId);
    }, 500);
  }
}

function onScanFailure(error) {
  // console.warn(`Code scan error = ${error}`);
}

function startCamera() {
  isScanning = true;
  html5QrCode = new Html5Qrcode("reader");

  html5QrCode.start(
    { facingMode: "environment" },
    {
      fps: 10,
      qrbox: { width: 250, height: 250 }
    },
    onScanSuccess,
    onScanFailure
  ).then(() => {
    $('#start-camera').hide();
    $('#stop-camera').show();
    showResult('info', 'Camera active. Point at QR code.');
  }).catch(err => {
    showResult('danger', 'Camera error: ' + err);
  });
}

function stopCamera() {
  isScanning = false;
  if (html5QrCode) {
    html5QrCode.stop().then(() => {
      $('#start-camera').show();
      $('#stop-camera').hide();
    }).catch(err => {
      console.error('Failed to stop camera', err);
    });
  }
}

function showResult(type, message) {
  var alertClass = 'alert-info';
  if (type === 'success') alertClass = 'alert-success';
  if (type === 'danger') alertClass = 'alert-danger';
  if (type === 'warning') alertClass = 'alert-warning';

  $('#result').html('<div class="alert ' + alertClass + ' text-center" style="font-size: 16px;">' + message + '</div>');
}

// Stop camera when leaving page
$(window).on('beforeunload', function() {
  stopCamera();
});
</script>
@endsection
