@extends('layouts.admin')
@section('title', 'QR Scan Result')
@section('content')
<h3 class="page-header">
  QR Scan Result
  <a href="{{ route('package_bookings.scan_form') }}" class="btn btn-success pull-right">Scan Another</a>
</h3>

<div class="row">
  <div class="col-md-8 col-md-offset-2">

    @if($status == 'invalid')
      <!-- Invalid QR -->
      <div class="panel panel-danger">
        <div class="panel-heading text-center">
          <h2><i class="nav-icon icon-close"></i> {{ $message }}</h2>
        </div>
        <div class="panel-body text-center">
          <p class="lead">The QR code you scanned does not exist in our system.</p>
          <p>Please check the code and try again.</p>
          <a href="{{ route('package_bookings.scan_form') }}" class="btn btn-primary btn-lg">
            <i class="nav-icon icon-refresh"></i> Try Again
          </a>
        </div>
      </div>

    @elseif($status == 'expired')
      <!-- Expired Booking -->
      <div class="panel panel-warning">
        <div class="panel-heading text-center">
          <h2><i class="nav-icon icon-warning"></i> {{ $message }}</h2>
        </div>
        <div class="panel-body">
          <div class="alert alert-warning text-center">
            <strong>This booking is not valid for today.</strong><br>
            <p>Booking Date: {{ isset($booking) ? date('d-m-Y', strtotime($booking->date)) : 'N/A' }}</p>
          </div>

          @if(isset($ticket))
            <table class="table table-bordered">
              <tr>
                <th>Ticket Name:</th>
                <td>{{ $ticket['ticket_name'] ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Ticket Token:</th>
                <td><code>{{ $ticket['ticket_token'] ?? 'N/A' }}</code></td>
              </tr>
            </table>
          @endif

          @if(isset($booking))
            <table class="table table-bordered">
              <tr>
                <th>Booking ID:</th>
                <td>#{{ $booking->id }}</td>
              </tr>
              <tr>
                <th>Package:</th>
                <td>{{ $booking->package ? $booking->package->name : 'N/A' }}</td>
              </tr>
              <tr>
                <th>Booking Date:</th>
                <td>{{ date('d-m-Y', strtotime($booking->date)) }}</td>
              </tr>
            </table>
          @endif

          <div class="text-center">
            <a href="{{ route('package_bookings.scan_form') }}" class="btn btn-primary btn-lg">
              <i class="nav-icon icon-refresh"></i> Scan Another
            </a>
          </div>
        </div>
      </div>

    @elseif($status == 'used')
      <!-- Already Used -->
      <div class="panel panel-warning">
        <div class="panel-heading text-center">
          <h2><i class="nav-icon icon-warning"></i> {{ $message }}</h2>
        </div>
        <div class="panel-body">
          <div class="alert alert-warning text-center">
            <strong>This ticket was already used on:</strong><br>
            <h3>{{ isset($used_at) ? date('d-m-Y h:i A', strtotime($used_at)) : 'N/A' }}</h3>
          </div>

          @if(isset($ticket))
            <table class="table table-bordered">
              <tr>
                <th>Ticket Name:</th>
                <td>{{ $ticket['ticket_name'] ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Ticket Token:</th>
                <td><code>{{ $ticket['ticket_token'] ?? 'N/A' }}</code></td>
              </tr>
            </table>
          @endif

          @if(isset($booking))
            <table class="table table-bordered">
              <tr>
                <th>Booking ID:</th>
                <td>#{{ $booking->id }}</td>
              </tr>
              <tr>
                <th>Package:</th>
                <td>{{ $booking->package ? $booking->package->name : 'N/A' }}</td>
              </tr>
              <tr>
                <th>Total Person:</th>
                <td>{{ $booking->total_person }}</td>
              </tr>
              <tr>
                <th>Booking Date:</th>
                <td>{{ date('d-m-Y', strtotime($booking->date)) }}</td>
              </tr>
            </table>
          @endif

          <div class="text-center">
            <a href="{{ route('package_bookings.scan_form') }}" class="btn btn-primary btn-lg">
              <i class="nav-icon icon-refresh"></i> Scan Another
            </a>
          </div>
        </div>
      </div>

    @elseif($status == 'success')
      <!-- Success -->
      <div class="panel panel-success">
        <div class="panel-heading text-center">
          <h2><i class="nav-icon icon-check"></i> {{ $message }}</h2>
        </div>
        <div class="panel-body">
          <div class="alert alert-success text-center">
            <strong>Entry Granted!</strong><br>
            <p>This ticket is valid and entry has been recorded.</p>
          </div>

          @if(isset($ticket))
            <h4 class="text-center">Ticket Details</h4>
            <table class="table table-bordered table-striped">
              <tr class="info">
                <th>Ticket Name</th>
                <td><strong>{{ $ticket['ticket_name'] ?? 'N/A' }}</strong></td>
              </tr>
              <tr>
                <th>Ticket Token</th>
                <td><code>{{ $ticket['ticket_token'] ?? 'N/A' }}</code></td>
              </tr>
              <tr>
                <th>Source</th>
                <td><span class="badge badge-info">{{ $ticket['source'] ?? 'N/A' }}</span></td>
              </tr>
            </table>
          @endif

          @if(isset($booking))
            <h4 class="text-center">Booking Details</h4>
            <table class="table table-bordered table-striped">
              <tr>
                <th>Booking ID</th>
                <td>#{{ $booking->id }}</td>
              </tr>
              <tr>
                <th>Package Name</th>
                <td><strong>{{ $booking->package ? $booking->package->name : 'N/A' }}</strong></td>
              </tr>
              <tr>
                <th>Total Person</th>
                <td><span class="badge badge-info">{{ $booking->total_person }}</span></td>
              </tr>
              <tr>
                <th>Package Quantity</th>
                <td>{{ $booking->quantity }}</td>
              </tr>
              <tr>
                <th>Booking Date</th>
                <td>{{ date('d-m-Y', strtotime($booking->date)) }}</td>
              </tr>
              <tr>
                <th>Booking Token</th>
                <td><code>{{ $booking->booking_token ?? 'N/A' }}</code></td>
              </tr>
            </table>

            @if($booking->items->count() > 0)
              <h4 class="text-center">Included Tickets</h4>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Ticket Name</th>
                    <th>Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  @php $index = 1; @endphp
                  @foreach($booking->items->where('source', 'package') as $item)
                    <tr>
                      <td>{{ $index++ }}</td>
                      <td>{{ $item->ticket ? $item->ticket->name : 'N/A' }}</td>
                      <td>{{ $item->quantity }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @endif
          @endif

          <div class="text-center">
            <a href="{{ route('package_bookings.scan_form') }}" class="btn btn-primary btn-lg">
              <i class="nav-icon icon-refresh"></i> Scan Another
            </a>
          </div>
        </div>
      </div>
    @endif

  </div>
</div>

@endsection
