@extends('layouts.admin')
@section('title', 'Locker & Gear Dashboard')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-chart-pie mr-2"></i>Locker & Gear Dashboard</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Locker & Gear Reports</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Date Filter -->
<div class="card card-outline card-primary mb-4">
  <div class="card-body">
    {{ Form::open(['route' => 'locker_gear_reports.index', 'method' => 'GET', 'class' => 'form-inline']) }}
    <div class="form-group mr-2">
      <label class="mr-2">Counter:</label>
      {{ Form::select('locker_gear_counter_id', ['' => 'All Counters'] + $counters, request('locker_gear_counter_id'), ['class' => 'form-control']) }}
    </div>
    <div class="form-group mr-2">
      <label class="mr-2">From:</label>
      <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
    </div>
    <div class="form-group mr-2">
      <label class="mr-2">To:</label>
      <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
    </div>
    <button type="submit" class="btn btn-primary">
      <i class="fa fa-filter mr-1"></i>Filter
    </button>
    {{ Form::close() }}
  </div>
</div>

<!-- Stats Cards -->
<div class="row text-center">
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $totalTickets }}</h3>
        <p>Total Tickets</p>
      </div>
      <div class="icon"><i class="fa fa-ticket-alt"></i></div>
    </div>
  </div>
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ number_format($totalRevenue, 0) }}</h3>
        <p>Total Revenue (Tk)</p>
      </div>
      <div class="icon"><i class="fa fa-money-bill"></i></div>
    </div>
  </div>
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ number_format($totalExtra, 0) }}</h3>
        <p>Extra Charges (Tk)</p>
      </div>
      <div class="icon"><i class="fa fa-exclamation-circle"></i></div>
    </div>
  </div>
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="small-box bg-primary">
      <div class="inner">
        <h3>{{ $activeRentals }}</h3>
        <p>Active Rentals</p>
      </div>
      <div class="icon"><i class="fa fa-clock"></i></div>
    </div>
  </div>
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="small-box bg-secondary">
      <div class="inner">
        <h3>{{ $completedRentals }}</h3>
        <p>Completed</p>
      </div>
      <div class="icon"><i class="fa fa-check-circle"></i></div>
    </div>
  </div>
</div>

<!-- Recent Tickets -->
<div class="card card-outline card-primary">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-list mr-2"></i>Recent Tickets</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Ticket #</th>
            <th>Counter</th>
            <th>Items</th>
            <th>Status</th>
            <th>Entry Time</th>
            <th>Amount</th>
            <th>Created By</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentTickets as $ticket)
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
            <td>
              @if($ticket->status == 'checked_in')
                <span class="badge badge-success">Checked In</span>
              @else
                <span class="badge badge-secondary">Checked Out</span>
              @endif
            </td>
            <td>{{ $ticket->entry_time ? $ticket->entry_time->format('d M Y, h:i A') : 'N/A' }}</td>
            <td>{{ number_format($ticket->total_amount, 2) }} Tk</td>
            <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-3">No recent tickets</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
