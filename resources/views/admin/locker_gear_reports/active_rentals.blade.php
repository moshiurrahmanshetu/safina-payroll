@extends('layouts.admin')
@section('title', 'Active Rentals')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-clock mr-2"></i>Active Rentals</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Active Rentals</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Counter Filter -->
<div class="card card-outline card-primary mb-4">
  <div class="card-body">
    {{ Form::open(['route' => 'locker_gear_reports.active_rentals', 'method' => 'GET', 'class' => 'form-inline']) }}
    <div class="form-group mr-2">
      <label class="mr-2">Counter:</label>
      {{ Form::select('locker_gear_counter_id', ['' => 'All Counters'] + $counters, request('locker_gear_counter_id'), ['class' => 'form-control']) }}
    </div>
    <button type="submit" class="btn btn-primary">
      <i class="fa fa-filter mr-1"></i>Filter
    </button>
    {{ Form::close() }}
  </div>
</div>

<!-- Stats -->
<div class="alert alert-info">
  <i class="fa fa-info-circle mr-2"></i>Total Active Rentals: <strong>{{ count($activeRentals) }}</strong>
</div>

<!-- Active Rentals Table -->
<div class="card card-outline card-primary">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-list mr-2"></i>Currently Rented Items</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Ticket #</th>
            <th>Counter</th>
            <th>Items</th>
            <th>Entry Time</th>
            <th>Duration</th>
            <th>Created By</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($activeRentals as $ticket)
          <tr>
            <td><strong>{{ $ticket->ticket_number }}</strong></td>
            <td><span class="badge badge-info">{{ $ticket->lockerGearCounter->name ?? 'N/A' }}</span></td>
            <td>
                @foreach($ticket->items as $item)
                    @if($item->item_type == 'locker')
                        <span class="badge badge-info">
                            <i class="fa fa-lock mr-1"></i>
                            {{ $item->locker->name ?? 'N/A' }}
                        </span>
                    @else
                        <span class="badge badge-warning">
                            <i class="fa fa-tshirt mr-1"></i>
                            {{ $item->gear->name ?? 'N/A' }} x{{ $item->quantity }}
                        </span>
                    @endif
                @endforeach
            </td>
            <td>{{ $ticket->entry_time ? $ticket->entry_time->format('d M Y, h:i A') : 'N/A' }}</td>
            <td>{{ $ticket->entry_time ? now()->diffForHumans($ticket->entry_time, true) : 'N/A' }}</td>
            <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
            <td>
              <a href="{{ route('locker_gear_tickets.scan', $ticket->ticket_number) }}" class="btn btn-primary btn-sm">
                <i class="fa fa-eye"></i> View
              </a>
              <a href="{{ route('locker_gear_tickets.show', $ticket->ticket_number) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-print"></i> Print
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <i class="fa fa-check-circle fa-2x mb-2 text-success"></i><br>
              No active rentals - all items returned
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
