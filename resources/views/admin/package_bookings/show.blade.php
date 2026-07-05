@extends('layouts.admin')
@section('title', 'Package Booking Details')
@section('content')
<h3 class="page-header">
  Package Booking Details
  <div class="pull-right">
    <a href="{{ route('package_bookings.preview', $booking->id) }}" class="btn btn-info">
      <i class="icon-eye"></i> Preview Tickets
    </a>
    <a href="{{ route('package_bookings.print_tickets', $booking->id) }}" class="btn btn-primary" target="_blank">
      <i class="icon-printer"></i> Print Tickets
    </a>
    {{link_to_route('package_bookings.index', 'Back to List', [], array('class'=>'btn btn-success'))}}
  </div>
</h3>

<div class="row">
  <div class="col-md-8 col-md-offset-2">

    <!-- Booking Summary -->
    <div class="panel panel-primary">
      <div class="panel-heading">
        <strong>Booking #{{ $booking->id }}</strong>
        <span class="pull-right">{{ date('d-m-Y', strtotime($booking->date)) }}</span>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-condensed">
              <tr>
                <th>Package:</th>
                <td>{{ $booking->package ? $booking->package->name : 'N/A' }}</td>
              </tr>
              <tr>
                <th>Quantity:</th>
                <td>{{ $booking->quantity }}</td>
              </tr>
              <tr>
                <th>Total Person:</th>
                <td>{{ $booking->total_person }}</td>
              </tr>
              <tr>
                <th>Counter:</th>
                <td>{{ $booking->counter ? $booking->counter->name : '-' }}</td>
              </tr>
              <tr>
                <th>Package Counter:</th>
                <td>{{ $booking->packageCounter ? $booking->packageCounter->name : '-' }}</td>
              </tr>
              <tr>
                <th>Created By:</th>
                <td>{{ $booking->creator ? $booking->creator->name : '-' }}</td>
              </tr>
              <tr>
                <th>Entry Status:</th>
                <td>
                  @if($booking->is_used)
                    <span class="label label-danger">USED</span>
                    <small>({{ date('d-m-Y h:i A', strtotime($booking->used_at)) }})</small>
                  @else
                    <span class="label label-success">NOT USED</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>Ticket Status:</th>
                <td>
                  @if($booking->ticket_status === 'printed')
                    <span class="label label-info">PRINTED</span>
                  @else
                    <span class="label label-warning">DRAFT</span>
                  @endif
                </td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <!-- QR Code Display -->
            <div class="panel panel-info">
              <div class="panel-heading text-center">Entry QR Code</div>
              <div class="panel-body text-center">
                @if($booking->qr_code)
                  <div style="background: white; padding: 10px; display: inline-block;">
                    {!! QrCode::size(200)->generate($booking->qr_code) !!}
                  </div>
                  <p class="mt-2"><code>{{ $booking->qr_code }}</code></p>
                @else
                  <p class="text-muted">No QR code generated</p>
                @endif
              </div>
            </div>
            <div class="panel panel-success">
              <div class="panel-heading text-center">Amount Breakdown</div>
              <div class="panel-body">
                <table class="table table-condensed">
                  <tr>
                    <td>Base Amount:</td>
                    <td class="text-right">৳{{ number_format($booking->base_amount, 2) }}</td>
                  </tr>
                  <tr>
                    <td>Extra Person ({{ $booking->extra_person }}):</td>
                    <td class="text-right">৳{{ number_format($booking->extra_amount, 2) }}</td>
                  </tr>
                  <tr class="success">
                    <td><strong>Final Amount:</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($booking->final_amount, 2) }}</strong></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tickets List -->
    <div class="panel panel-default">
      <div class="panel-heading">Tickets</div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Ticket</th>
              <th>Source</th>
              <th>Qty</th>
              <th>Unit Price</th>
              <th class="text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            @php
              $packageItems = $booking->items->where('source', 'package');
              $index = 1;
            @endphp

            @if($packageItems->count() > 0)
              <tr class="info">
                <td colspan="6"><strong>Included in Package</strong></td>
              </tr>
              @foreach($packageItems as $item)
              <tr>
                <td>{{ $index++ }}</td>
                {{-- ticket() returns Ticket model via service_id column --}}
                <td>{{ $item->ticket ? $item->ticket->name : 'N/A' }}</td>
                <td><span class="label label-info">Package</span></td>
                <td>{{ $item->quantity }}</td>
                <td>-</td>
                <td class="text-right">Included</td>
              </tr>
              @endforeach
            @endif

            @if($booking->items->count() === 0)
              <tr>
                <td colspan="6" class="text-center text-muted">No tickets</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

@endsection
