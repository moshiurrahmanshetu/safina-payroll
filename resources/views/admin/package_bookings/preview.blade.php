@extends('layouts.admin')
@section('title', 'Preview Package Tickets')
@section('content')
<h3 class="page-header">
  Preview Tickets - Booking #{{ $booking->id }}
  <div class="pull-right">
    <a href="{{ route('package_bookings.print_tickets', $booking->id) }}" class="btn btn-primary" target="_blank">
      <i class="icon-printer"></i> Print Now
    </a>
    <a href="{{ route('package_bookings.show', $booking->id) }}" class="btn btn-success">Back to Details</a>
  </div>
</h3>

<div class="row">
  <div class="col-md-12">
    <!-- Booking Summary -->
    <div class="panel panel-info">
      <div class="panel-heading">
        <strong>Booking Summary</strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-3">
            <strong>Package:</strong> {{ $booking->package ? $booking->package->name : 'N/A' }}
          </div>
          <div class="col-md-3">
            <strong>Date:</strong> {{ date('d-m-Y', strtotime($booking->date)) }}
          </div>
          <div class="col-md-3">
            <strong>Total Person:</strong> {{ $booking->total_person }}
          </div>
          <div class="col-md-3">
            <strong>Final Amount:</strong> ৳{{ number_format($booking->final_amount, 2) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Tickets Preview -->
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>Individual Tickets ({{ count($tickets) }} tickets)</strong>
      </div>
      <div class="panel-body">
        <div class="row">
          @foreach($tickets as $index => $ticket)
          <div class="col-md-3">
            <div class="panel {{ stripos($ticket['ticket_name'], 'entry') !== false ? 'panel-success' : 'panel-default' }}">
              <div class="panel-heading text-center">
                <strong>{{ $ticket['ticket_name'] }}</strong>
                @if(stripos($ticket['ticket_name'], 'entry') !== false)
                <span class="label label-success">ENTRY</span>
                @endif
              </div>
              <div class="panel-body text-center">
                <p><strong>TOKEN: {{ $ticket['ticket_token'] }}</strong></p>
                <p>Status: {!! $ticket['is_used'] ? '<span class="label label-danger">USED</span>' : '<span class="label label-success">VALID</span>' !!}</p>

                @if($ticket['ticket_token'])
                <div style="background: white; padding: 10px; display: inline-block; margin: 10px 0;">
                  {!! QrCode::size(120)->generate(route('package.scan.token', ['package_token' => $booking->booking_token, 'ticket_token' => $ticket['ticket_token']])) !!}
                </div>
                <p class="text-muted" style="font-size: 9px; word-break: break-all;">{{ $ticket['ticket_token'] }}</p>
                @endif

                @if($ticket['is_used'])
                <p class="text-muted" style="font-size: 11px;">Used at: {{ date('d-m-Y h:i A', strtotime($ticket['used_at'])) }}</p>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Print Button -->
    <div class="text-center" style="margin: 30px 0;">
      <a href="{{ route('package_bookings.print_tickets', $booking->id) }}" class="btn btn-lg btn-primary" target="_blank">
        <i class="icon-printer"></i> Print Tickets Now
      </a>
    </div>

  </div>
</div>
@endsection
