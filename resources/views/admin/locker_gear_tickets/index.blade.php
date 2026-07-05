@extends('layouts.admin')
@section('title', 'Locker & Gear Tickets')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-ticket-alt mr-2"></i>Locker & Gear Tickets @if($tickets) ({{count($tickets)}}) @endif</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Locker & Gear Tickets</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card card-outline card-primary shadow mb-4">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>Ticket List</h3>
    <div class="card-tools">
      @if(checkMenuActive('LockerGearTicketController@create', $menu_list))
      <a href="{{ route('locker_gear_tickets.create') }}" class="btn btn-success btn-sm">
        <i class="fa fa-plus-circle mr-1"></i> Create Ticket
      </a>
      @endif
    </div>
  </div>
  <div class="panel-body p-3">
    {{ Form::open(['route' => 'locker_gear_tickets.index', 'method' => 'GET']) }}

    <div class="row">

        <!-- Counter -->
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Counter</label>
                {{ Form::select(
                    'locker_gear_counter_id',
                    ['' => 'All Counters'] + $counters,
                    request('locker_gear_counter_id'),
                    ['class' => 'form-control form-control-sm']
                ) }}
            </div>
        </div>

        <!-- Status -->
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">Status</label>
                {{ Form::select(
                    'status',
                    [
                        '' => 'All',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out'
                    ],
                    request('status'),
                    ['class' => 'form-control form-control-sm']
                ) }}
            </div>
        </div>

        <!-- Date -->
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">Date</label>
                <input
                    type="date"
                    name="date"
                    class="form-control form-control-sm"
                    value="{{ request('date') }}"
                >
            </div>
        </div>

        <!-- Buttons -->
        <div class="col-md-2">
            <div class="form-group" style="margin-top:25px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-filter mr-1"></i>Filter
                </button>

                <a href="{{ route('locker_gear_tickets.index') }}" class="btn btn-danger btn-sm">
                    <i class="fa fa-times mr-1"></i>Clear
                </a>
            </div>
        </div>

    </div>

    {{ Form::close() }}
</div>
</div>

<div class="card card-outline card-primary shadow">
  <div class="card-body p-0">
    @if(session('flash_success'))
    <div class="alert alert-success m-3">
      <i class="fa fa-check-circle mr-2"></i>{{ session('flash_success') }}
    </div>
    @endif

    @if(session('flash_error'))
    <div class="alert alert-danger m-3">
      <i class="fa fa-exclamation-circle mr-2"></i>{{ session('flash_error') }}
    </div>
    @endif

    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead>
          <tr>
            <th>Ticket #</th>
            <th>Counter</th>
            <th>Items</th>
            <th>Status</th>
            <th>Entry Time</th>
            <th>Amount</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tickets as $ticket)
          <tr>
            <td><strong>{{ $ticket->ticket_number }}</strong></td>
            <td>
              <span class="badge badge-info">{{ $ticket->lockerGearCounter->name ?? 'N/A' }}</span>
            </td>
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
            <td>
              {{ number_format($ticket->total_amount, 2) }} Tk
              @if($ticket->extra_amount > 0)
                <br><small class="text-danger">+{{ number_format($ticket->extra_amount, 2) }} extra</small>
              @endif
            </td>
            <td>
              <a href="{{ route('locker_gear_tickets.show', $ticket->ticket_number) }}" class="btn btn-primary btn-sm">
                <i class="fa fa-eye"></i> View
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <i class="fa fa-inbox fa-2x mb-2"></i><br>
              No tickets found
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($tickets->hasPages())
  <div class="card-footer">
    {{ $tickets->links() }}
  </div>
  @endif
</div>

@endsection
