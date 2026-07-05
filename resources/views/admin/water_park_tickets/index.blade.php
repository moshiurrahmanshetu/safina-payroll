@extends('layouts.admin')
@section('title', 'Water Park Tickets')
@section('content')


<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-swimmer mr-2"></i>Water Park Tickets @if($tickets) ({{count($tickets)}}) @endif </h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Water Park Tickets</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card card-outline card-primary shadow">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>Ticket Records</h3>
    <div class="card-tools">
      @if(checkMenuActive('WaterParkTicketController@create', $menu_list))
      <a href="{{ route('water_park_tickets.create') }}" class="btn btn-success btn-sm">
        <i class="fa fa-plus-circle mr-1"></i> Create Ticket
      </a>
      @endif
    </div>
  </div>

  <div class="card-body p-0">
    
    <div class="panel-body p-3">
    {{ Form::open(['route' => 'water_park_tickets.index', 'method' => 'GET']) }}

    <div class="row">

        <!-- Counter -->
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">Counter</label>
                {{ Form::select(
                    'water_park_counter_id',
                    $counters,
                    request('water_park_counter_id'),
                    ['class' => 'form-control', 'placeholder' => '-- All Counters --']
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
                        '' => '-- All Status --',
                        'pending' => 'Pending',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out'
                    ],
                    request('status'),
                    ['class' => 'form-control']
                ) }}
            </div>
        </div>

        <!-- From Date -->
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">From Date</label>
                {{ Form::date(
                    'date_from',
                    request('date_from'),
                    ['class' => 'form-control']
                ) }}
            </div>
        </div>

        <!-- To Date -->
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">To Date</label>
                {{ Form::date(
                    'date_to',
                    request('date_to'),
                    ['class' => 'form-control']
                ) }}
            </div>
        </div>

        <!-- Buttons -->
        <div class="col-md-2">
            <div class="form-group" style="margin-top:25px;">
                <button type="submit" class="btn btn-primary">
                    Filter
                </button>

                <a href="{{ route('water_park_tickets.index') }}" class="btn btn-danger">
                    Reset
                </a>
            </div>
        </div>

    </div>

    {{ Form::close() }}
</div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead>
          <tr>
            <th>Ticket #</th>
            <th>Counter</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Status</th>
            <th>Created</th>
            <th>Created By</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tickets as $ticket)
          <tr>
            <td><strong>{{ $ticket->ticket_number }}</strong></td>
            <td>{{ $ticket->waterParkCounter->name ?? 'N/A' }}</td>
            <td><span class="badge badge-info">{{ $ticket->duration_minutes }} min</span></td>
            <td>{{ number_format($ticket->price, 2) }} Tk</td>
            <td>
              @if($ticket->status == 'pending')
                <span class="badge badge-warning">Pending</span>
              @elseif($ticket->status == 'checked_in')
                <span class="badge badge-info">Checked In</span>
              @else
                <span class="badge badge-success">Checked Out</span>
              @endif
            </td>
            <td>{{ $ticket->created_at->format('d M Y, h:i A') }}</td>
            <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
              <p>No tickets found.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($tickets->hasPages())
    <div class="card-footer">
      <div class="d-flex justify-content-center">
        {{ $tickets->links() }}
      </div>
    </div>
    @endif
  </div>
</div>

@endsection
