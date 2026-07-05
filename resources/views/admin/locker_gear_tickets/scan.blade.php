@extends('layouts.admin')
@section('title', 'Ticket Details')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-ticket-alt mr-2"></i>Ticket #{{ $ticket->ticket_number }}</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Scan Result</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if(session('flash_success'))
  @if(strpos(session('flash_success'), '0.00') !== false || strpos(session('flash_success'), 'Extra charges: 0') !== false)
  <div class="alert alert-success">
    <i class="fa fa-check-circle mr-2"></i>{{ session('flash_success') }}
    <br><small class="ml-4">No extra charges - returned on time!</small>
  </div>
  @else
  <div class="alert alert-warning">
    <i class="fa fa-exclamation-triangle mr-2"></i>{{ session('flash_success') }}
    <br><small class="ml-4">Extra charges applied for overtime</small>
  </div>
  @endif
@endif

@if(session('flash_error'))
<div class="alert alert-danger">
  <i class="fa fa-exclamation-circle mr-2"></i>{{ session('flash_error') }}
</div>
@endif

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-outline card-primary shadow">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-info-circle mr-2"></i>Ticket Information</h3>
        <div class="card-tools">
          @if($ticket->status == 'checked_in')
            <span class="badge badge-success badge-lg">CHECKED IN</span>
          @else
            <span class="badge badge-secondary badge-lg">CHECKED OUT</span>
          @endif
        </div>
      </div>
      <div class="card-body">
        <!-- Ticket Info -->
        <table class="table table-bordered">
          <tr>
            <th style="width: 40%"><i class="fa fa-ticket mr-1"></i> Ticket Number</th>
            <td><strong>{{ $ticket->ticket_number }}</strong></td>
          </tr>
          <tr>
            <th><i class="fa fa-building mr-1"></i> Counter</th>
            <td><strong>{{ $ticket->lockerGearCounter->name ?? 'N/A' }}</strong></td>
          </tr>
          <tr>
            <th><i class="fa fa-clock mr-1"></i> Entry Time</th>
            <td>{{ $ticket->entry_time ? $ticket->entry_time->format('d M Y, h:i A') : 'N/A' }}</td>
          </tr>
          @if($ticket->exit_time)
          <tr>
            <th><i class="fa fa-clock-o mr-1"></i> Exit Time</th>
            <td>{{ $ticket->exit_time->format('d M Y, h:i A') }}</td>
          </tr>
          @endif
          <tr>
            <th><i class="fa fa-user mr-1"></i> Created By</th>
            <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
          </tr>
        </table>

        <!-- Items -->
        <h5 class="mb-3"><i class="fa fa-box mr-2"></i>Rented Items</h5>
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Type</th>
              <th>Item</th>
              <th>Quantity</th>
            </tr>
          </thead>
          <tbody>
            @foreach($ticket->items as $item)
            <tr>
              <td>
                @if($item->item_type == 'locker')
                  <span class="badge badge-info"><i class="fa fa-lock mr-1"></i> Locker</span>
                @else
                  <span class="badge badge-warning"><i class="fa fa-tshirt mr-1"></i> Gear</span>
                @endif
              </td>
              <td>
                <strong>
                    @if($item->item_type == 'locker')
                        {{ $item->locker->name ?? 'N/A' }}
                    @else
                        {{ $item->gear->name ?? 'N/A' }}
                    @endif
                </strong>
              </td>
              <td>{{ $item->quantity }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>



        <!-- Billing Summary -->
        <div class="alert alert-light border mt-4">
          <h6 class="font-weight-bold"><i class="fa fa-money mr-2"></i>Billing Summary</h6>
          <table class="table table-sm mb-0">
            <tr>
              <td>Base Amount:</td>
              <td class="text-right">{{ number_format($ticket->total_amount - $ticket->extra_amount, 2) }} Tk</td>
            </tr>
            @if($ticket->extra_amount > 0)
            <tr>
              <td>Extra Charges:</td>
              <td class="text-right text-danger">+{{ number_format($ticket->extra_amount, 2) }} Tk</td>
            </tr>
            @endif
            <tr class="font-weight-bold">
              <td>Total Amount:</td>
              <td class="text-right">{{ number_format($ticket->total_amount, 2) }} Tk</td>
            </tr>
          </table>
        </div>

        <!-- Actions -->
        <div class="text-center mt-4">
          @if($ticket->status == 'checked_in')
            @if(checkMenuActive('LockerGearTicketController@checkOut', $menu_list))
            {{ Form::open(['route' => ['locker_gear_tickets.check_out', $ticket->ticket_number], 'method' => 'POST', 'style' => 'display:inline']) }}
            <button type="submit" class="btn btn-danger btn-lg">
              <i class="fa fa-sign-out-alt mr-2"></i>CHECK OUT
            </button>
            {{ Form::close() }}
            @endif
          @else
            <div class="alert alert-success">
              <i class="fa fa-check-circle mr-2"></i>Ticket checked out successfully.
            </div>
          @endif

          <a href="{{ route('locker_gear_tickets.scan_camera') }}" class="btn btn-secondary ml-2">
            <i class="fa fa-camera mr-2"></i>Scan Another
          </a>

          <a href="{{ route('locker_gear_tickets.show', $ticket->ticket_number) }}" class="btn btn-outline-primary ml-2">
            <i class="fa fa-print mr-2"></i>Print Ticket
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
