@extends('layouts.admin')
@section('title', 'Water Park Ticket - ' . $ticket->ticket_number)
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-qrcode mr-2"></i>Ticket Scan Result</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('water_park_tickets.index') }}">Tickets</a></li>
          <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <!-- Status Card -->
    <div class="card shadow mb-4">
      <div class="card-header text-center py-4
        @if($ticket->status == 'pending') bg-secondary text-white
        @elseif($ticket->status == 'checked_in') bg-success text-white
        @else bg-dark text-white
        @endif">
        <h3 class="mb-1">{{ $ticket->ticket_number }}</h3>
        <p class="mb-0">
          @if($ticket->status == 'pending')
            <i class="fa fa-clock mr-2"></i>Pending - Check-in Required
          @elseif($ticket->status == 'checked_in')
            <i class="fa fa-check-circle mr-2"></i>Checked In
          @else
            <i class="fa fa-check-circle mr-2"></i>Completed
          @endif
        </p>
      </div>
      <div class="card-body">
        <!-- Ticket Info -->
        <table class="table table-bordered">
          <tr>
            <th style="width: 40%"><i class="fa fa-desktop mr-1"></i> Counter</th>
            <td>{{ $ticket->waterParkCounter->name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th><i class="fa fa-clock mr-1"></i> Time Package</th>
            <td>
              <span class="badge badge-info">{{ $ticket->duration_minutes }} Minutes</span>
              <small class="text-muted">(Fixed duration)</small>
            </td>
          </tr>
          <tr>
            <th><i class="fa fa-money-bill mr-1"></i> Base Price</th>
            <td>{{ number_format($ticket->price, 2) }} Tk</td>
          </tr>
          <tr>
            <th><i class="fa fa-user mr-1"></i> Created By</th>
            <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th><i class="fa fa-calendar mr-1"></i> Created At</th>
            <td>{{ $ticket->created_at->format('d M Y, h:i A') }}</td>
          </tr>
        </table>

        <!-- Check In Info -->
        @if($ticket->entry_time)
        <div class="alert alert-info">
          <h5><i class="fa fa-sign-in-alt mr-2"></i>Check In Info</h5>
          <p class="mb-0">Entry Time: <strong>{{ $ticket->entry_time->format('d M Y, h:i A') }}</strong></p>
        </div>
        @endif

        <!-- Check Out Info (if completed) -->
        @if($ticket->status == 'checked_out')
        <div class="alert alert-success">
          <h5><i class="fa fa-sign-out-alt mr-2"></i>Check Out Summary</h5>
          <table class="table table-sm table-borderless mb-0">
            <tr>
              <td>Exit Time:</td>
              <td><strong>{{ $ticket->exit_time->format('d M Y, h:i A') }}</strong></td>
            </tr>
            <tr>
              <td>Allowed Time:</td>
              <td>{{ $ticket->duration_minutes }} min</td>
            </tr>
            @php
              $usedMinutes = $ticket->entry_time->diffInMinutes($ticket->exit_time);
              $allowedMinutes = $ticket->duration_minutes;
            @endphp
            <tr>
              <td>Used Time:</td>
              <td>{{ $usedMinutes }} min</td>
            </tr>
            @if($ticket->extra_minutes > 0)
            <tr class="table-danger">
              <td>Extra Time:</td>
              <td><strong>{{ $ticket->extra_minutes }} min</strong></td>
            </tr>
            <tr class="table-danger">
              <td>Extra Charge:</td>
              <td><strong class="text-danger">{{ number_format($ticket->extra_amount, 2) }} Tk</strong></td>
            </tr>
            @endif
            <tr class="table-success">
              <td><strong>Total Amount:</strong></td>
              <td><strong class="text-success">{{ number_format($ticket->price + $ticket->extra_amount, 2) }} Tk</strong></td>
            </tr>
          </table>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="text-center mt-4">
          @if($ticket->status == 'pending')
            {{ Form::open(['route' => ['water_park_tickets.check_in', $ticket->ticket_number], 'method' => 'POST', 'class' => 'd-inline']) }}
              <button type="submit" class="btn btn-success btn-lg btn-block">
                <i class="fa fa-sign-in-alt mr-2"></i>CHECK IN
              </button>
            {{ Form::close() }}
          @elseif($ticket->status == 'checked_in')
            {{ Form::open(['route' => ['water_park_tickets.check_out', $ticket->ticket_number], 'method' => 'POST', 'class' => 'd-inline']) }}
              <button type="submit" class="btn btn-danger btn-lg btn-block"
                onclick="return confirm('Are you sure you want to check out this ticket?');">
                <i class="fa fa-sign-out-alt mr-2"></i>CHECK OUT
              </button>
            {{ Form::close() }}
          @endif
        </div>

        <!-- Back Button -->
        <div class="text-center mt-3">
          <a href="{{ route('water_park_tickets.scan_camera') }}" class="btn btn-success">
            <i class="fa fa-camera mr-1"></i> Scan Another
          </a>
          <a href="{{ route('water_park_tickets.index') }}" class="btn btn-info ml-2">
            <i class="fa fa-list mr-1"></i> Ticket List
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
