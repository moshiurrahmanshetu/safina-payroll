@extends('layouts.admin')
@section('title', 'Scan Package QR Code')
@section('content')
<h3 class="page-header">
  Scan Package QR Code
  {{link_to_route('package_bookings.index', 'Back to List', [], array('class'=>'btn btn-success pull-right'))}}
</h3>

<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h4><i class="nav-icon icon-qr"></i> QR Code Scanner</h4>
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
            Point camera at QR code on ticket. It will auto-validate.
          </p>
        </div>

        <hr style="margin: 30px 0;">

        <!-- Manual Input (Backup) -->
        <div class="manual-section">
          <h5><i class="nav-icon icon-keyboard"></i> Manual Entry</h5>
          <form id="scan-form" class="form-horizontal">
            <div class="form-group">
              <label>QR Code</label>
              <input type="text" name="qr_code" id="qr_code" class="form-control input-lg" placeholder="Enter QR Code (e.g., PKGxxxxxxxx)" autofocus>
            </div>
            <button type="submit" class="btn btn-info btn-block btn-lg">
              <i class="nav-icon icon-magnifier"></i> Validate Entry
            </button>
          </form>
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

// Handle manual form submission
$('#scan-form').on('submit', function(e) {
  e.preventDefault();
  var qrCode = $('#qr_code').val().trim();

  if (!qrCode) {
    showResult('danger', 'Please enter QR code');
    return;
  }

  validateQRCode(qrCode);
});

function validateQRCode(qrCode) {
  // Parse QR code URL to extract tokens
  var bookingToken = null;
  var ticketToken = null;

  // Check if QR code is a URL with path parameters
  if (qrCode.includes('/package/scan/')) {
    var parts = qrCode.split('/package/scan/');
    if (parts.length > 1) {
      var tokens = parts[1].split('/');
      if (tokens.length >= 2) {
        bookingToken = tokens[0];
        ticketToken = tokens[1];
      }
    }
  } else if (qrCode.includes('/package/scan')) {
    // Legacy format with query parameters
    var url = new URL(qrCode, window.location.origin);
    bookingToken = url.searchParams.get('p');
    ticketToken = url.searchParams.get('t');
  } else {
    // Legacy format - try to parse as booking_token:ticket_token
    var parts = qrCode.split(':');
    if (parts.length === 2) {
      bookingToken = parts[0];
      ticketToken = parts[1];
    }
  }

  if (!bookingToken || !ticketToken) {
    showResult('danger', 'Invalid QR code format. Expected URL format or booking_token:ticket_token');
    return;
  }

  $.ajax({
    url: '{{ route("package.validate.token") }}',
    type: 'POST',
    data: {
      p: bookingToken,
      t: ticketToken,
      _token: '{{ csrf_token() }}'
    },
    success: function(response) {
      if (response.status === 'valid') {
        showResult('success',
          '<strong>VALID ENTRY</strong><br>' +
          'Ticket: ' + response.ticket_name + '<br>' +
          'Package: ' + response.package_name + '<br>' +
          'Ticket Token: ' + response.ticket_token + '<br>' +
          'Booking ID: #' + response.booking_id + '<br>' +
          'Time: ' + response.scanned_at + '<br>' +
          '<span style="color: #5cb85c; font-size: 18px;">ENTRY ALLOWED</span>'
        );
      } else if (response.status === 'used') {
        showResult('warning',
          '<strong>ALREADY USED</strong><br>' +
          'This ticket has already been used<br>' +
          'Used at: ' + response.used_at + '<br>' +
          '<span style="color: #f0ad4e; font-size: 18px;">ENTRY DENIED</span>'
        );
      } else if (response.status === 'expired') {
        showResult('warning',
          '<strong>EXPIRED BOOKING</strong><br>' +
          'This booking is not valid for today<br>' +
          '<span style="color: #f0ad4e; font-size: 18px;">ENTRY DENIED</span>'
        );
      } else {
        showResult('danger',
          '<strong>INVALID QR CODE</strong><br>' +
          response.message + '<br>' +
          '<span style="color: #d9534f; font-size: 18px;">ENTRY DENIED</span>'
        );
      }

      $('#qr_code').val('').focus();
    },
    error: function(xhr) {
      if (xhr.status === 403) {
        showResult('danger', 'Unauthorized: You do not have permission to scan.');
      } else {
        showResult('danger', 'Error validating QR code. Please try again.');
      }
      $('#qr_code').val('').focus();
    }
  });
}

function onScanSuccess(decodedText, decodedResult) {
  // Stop scanning
  stopCamera();

  // Validate the QR code
  validateQRCode(decodedText);
}

function onScanFailure(error) {
  // console.warn(`Code scan error = ${error}`);
}

function startCamera() {
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
  }).catch(err => {
    showResult('danger', 'Camera error: ' + err);
  });
}

function stopCamera() {
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

  // Auto clear after 5 seconds for valid entries
  if (type === 'success') {
    setTimeout(function() {
      $('#result').fadeOut(500, function() {
        $(this).html('').show();
      });
    }, 5000);
  }
}
</script>
@endsection
