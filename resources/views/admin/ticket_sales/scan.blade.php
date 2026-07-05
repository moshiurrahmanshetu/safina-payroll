@extends('layouts.admin')
@section('title', 'Ticket Scan Validation')
@section('content')
<h3 class="page-header">Ticket Scan Validation</h3>

<!-- Flash Messages -->
@if(session('flash_success'))
  <div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Success!</strong> {{ session('flash_success') }}
  </div>
@endif
@if(session('flash_error'))
  <div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Error!</strong> {{ session('flash_error') }}
  </div>
@endif
@if(session('flash_warning'))
  <div class="alert alert-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Warning!</strong> {{ session('flash_warning') }}
  </div>
@endif

<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="panel panel-default">
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
          {{ Form::open(array('route' => 'ticket_sales.scan.validate', 'method'=>'POST', 'id'=>'scanForm')) }}
          <div class="form-group">
            <label>Ticket Number</label>
            <input type="text" name="ticket_number" id="ticket_number" class="form-control" placeholder="Enter ticket number manually" autofocus>
          </div>
          <button type="submit" class="btn btn-info btn-block">Validate Ticket</button>
          {{ Form::close() }}
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
$('#scanForm').on('submit', function(e) {
  e.preventDefault();
  var ticketNumber = $('#ticket_number').val().trim();

  if (!ticketNumber) {
    showResult('danger', 'Please enter ticket number');
    return;
  }

  validateTicket(ticketNumber);
});

function validateTicket(ticketNumber) {
  $.ajax({
    url: '{{ route("ticket_sales.scan.validate") }}',
    type: 'POST',
    data: {
      ticket_number: ticketNumber,
      _token: '{{ csrf_token() }}'
    },
    success: function(response) {
      if (response.status === 'valid') {
        showResult('success',
          '<strong>VALID TICKET</strong><br>' +
          'Ticket: ' + response.ticket + '<br>' +
          'QR Code: ' + response.qr_code + '<br>' +
          'Price: ' + parseFloat(response.price).toFixed(2) + ' Tk<br>' +
          '<span style="color: #5cb85c; font-size: 18px;">ENTRY ALLOWED</span>'
        );
      } else if (response.status === 'used') {
        showResult('warning',
          '<strong>ALREADY USED</strong><br>' +
          'Ticket: ' + response.ticket + '<br>' +
          'QR Code: ' + response.qr_code + '<br>' +
          'Used at: ' + response.used_at + '<br>' +
          '<span style="color: #f0ad4e; font-size: 18px;">ENTRY DENIED</span>'
        );
      } else {
        showResult('danger',
          '<strong>INVALID TICKET</strong><br>' +
          response.message + '<br>' +
          '<span style="color: #d9534f; font-size: 18px;">ENTRY DENIED</span>'
        );
      }

      $('#ticket_number').val('').focus();
    },
    error: function() {
      showResult('danger', 'Error validating ticket. Please try again.');
      $('#ticket_number').val('').focus();
    }
  });
}

function onScanSuccess(decodedText, decodedResult) {
  // Extract ticket number from URL if it's a URL
  var ticketNumber = decodedText;
  var urlPattern = /\/ticket\/verify\/(.+)$/;
  var match = decodedText.match(urlPattern);

  if (match) {
    ticketNumber = match[1];
  }

  // Stop scanning
  stopCamera();

  // Validate the ticket
  validateTicket(ticketNumber);
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

  // Auto clear after 5 seconds for valid tickets
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
