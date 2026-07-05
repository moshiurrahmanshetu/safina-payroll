@extends('layouts.admin')
@section('title', 'Overdue Report')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-exclamation-triangle mr-2 text-danger"></i>Overdue Rentals</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Overdue Report</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Counter Filter -->
<div class="card card-outline card-primary mb-4">
  <div class="card-body">
    {{ Form::open(['route' => 'locker_gear_reports.overdue', 'method' => 'GET', 'class' => 'form-inline']) }}
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
<div class="alert {{ count($overdueItems) > 0 ? 'alert-danger' : 'alert-success' }}">
  <i class="fa {{ count($overdueItems) > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' }} mr-2"></i>
  Total Overdue Items: <strong>{{ count($overdueItems) }}</strong>
</div>

<!-- Overdue Table -->
@if(count($overdueItems) > 0)
<div class="card card-outline card-danger">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>Overdue Items List</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Ticket #</th>
            <th>Counter</th>
            <th>Item</th>
            <th>Entry Time</th>
            <th>Expected Return</th>
            <th>Overdue By</th>
            <th>Expected Extra</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($overdueItems as $overdue)
          <tr>
            <td><strong>{{ $overdue['ticket']->ticket_number }}</strong></td>
            <td><span class="badge badge-info">{{ $overdue['ticket']->lockerGearCounter->name ?? 'N/A' }}</span></td>
            <td>
              @if($overdue['item']->item_type == 'locker')
                  <span class="badge badge-info">
                      <i class="fa fa-lock mr-1"></i>
                      {{ $overdue['item']->locker->name ?? 'N/A' }}
                  </span>
              @else
                  <span class="badge badge-warning">
                      <i class="fa fa-tshirt mr-1"></i>
                      {{ $overdue['item']->gear->name ?? 'N/A' }} x{{ $overdue['item']->quantity }}
                  </span>
              @endif
            </td>
            <td>{{ $overdue['entry_time']->format('d M Y, h:i A') }}</td>
            <td>{{ $overdue['expected_return']->format('d M Y, h:i A') }}</td>
            <td><span class="text-danger font-weight-bold">{{ floor($overdue['overtime_minutes'] / 60) }}h {{ $overdue['overtime_minutes'] % 60 }}m</span></td>
            <td><span class="text-danger font-weight-bold">+{{ number_format($overdue['expected_extra'], 2) }} Tk</span></td>
            <td>
              <a href="{{ route('locker_gear_tickets.scan', $overdue['ticket']->ticket_number) }}" class="btn btn-primary btn-sm">
                <i class="fa fa-eye"></i> View Ticket
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@else
<div class="card card-outline card-success">
  <div class="card-body text-center py-5">
    <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
    <h3>All Rentals On Time!</h3>
    <p class="text-muted">No overdue items found. All active rentals are within their allowed duration.</p>
  </div>
</div>
@endif

@endsection
