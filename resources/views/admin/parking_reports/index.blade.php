@extends('layouts.admin')
@section('title', 'Parking Reports')
@section('content')
<h3 class="page-header">
  Parking Reports
</h3>

<!-- Summary Cards -->
<div class="row mb-3">
  <div class="col-md-6">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <div class="row">
          <div class="col-8">
            <h4>Total Tickets</h4>
          </div>
          <div class="col-4 text-right">
            <h2>{{ $total_tickets }}</h2>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card bg-success text-white">
      <div class="card-body">
        <div class="row">
          <div class="col-8">
            <h4>Total Amount</h4>
          </div>
          <div class="col-4 text-right">
            <h2>{{ number_format($total_amount, 2) }} Tk</h2>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Panel -->
<div class="panel panel-default">
  <div class="panel-heading">
    <i class="nav-icon icon-magnifier"></i> Filter Options
    <span class="pull-right">
      <a href="{{ route('parking_reports.index') }}" class="btn btn-xs btn-danger">Reset</a>
    </span>
  </div>
  <div class="panel-body">
    <form method="GET" action="{{ route('parking_reports.index') }}" class="form-horizontal">
      <div class="row">
        <!-- From Date -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">From Date</label>
            <input type="date" name="date_from" class="form-control"
                   value="{{ request('date_from') }}">
          </div>
        </div>

        <!-- To Date -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">To Date</label>
            <input type="date" name="date_to" class="form-control"
                   value="{{ request('date_to') }}">
          </div>
        </div>

        <!-- Status Filter -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Status</label>
            <select name="status" class="form-control">
              <option value="">All Status</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
              <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
            </select>
          </div>
        </div>

        @if($canViewAll)
        <!-- User Filter (Admin only) -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Created By</label>
            <select name="user_id" class="form-control">
              <option value="">All Users</option>
              @foreach($users as $id => $name)
                <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                  {{ $name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        @endif
      </div>

      <div class="row">
        <div class="col-md-12 text-right">
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-filter"></i> Apply Filters
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Results Table -->
<div class="card mt-3">
  <div class="card-header">
    <i class="fa fa-list"></i> Parking Ticket List
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-bordered" id="parkingReportTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Ticket Number</th>
            <th>Vehicle</th>
            <th>Driver</th>
            <th>Status</th>
            <th>Entry Time</th>
            <th>Exit Time</th>
            <th>Duration</th>
            <th>Amount</th>
            <th>Created By</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          @forelse($parking_tickets as $ticket)
          <tr>
            <td>{{ $loop->iteration + ($parking_tickets->currentPage() - 1) * $parking_tickets->perPage() }}</td>
            <td><strong>{{ $ticket->ticket_number }}</strong></td>
            <td>
              <span class="badge badge-info">{{ $ticket->vehicle->name ?? 'N/A' }}</span>
              <br><small>{{ $ticket->vehicle_number }}</small>
            </td>
            <td>
              {{ $ticket->driver_name ?? 'N/A' }}
              @if($ticket->driver_phone)
                <br><small>{{ $ticket->driver_phone }}</small>
              @endif
            </td>
            <td>
              @if($ticket->status == 'pending')
                <span class="badge badge-secondary">Pending</span>
              @elseif($ticket->status == 'checked_in')
                <span class="badge badge-success">Checked In</span>
              @elseif($ticket->status == 'checked_out')
                <span class="badge badge-dark">Checked Out</span>
              @endif
            </td>
            <td>{{ $ticket->entry_time ? $ticket->entry_time->format('d-m-Y H:i') : '-' }}</td>
            <td>{{ $ticket->exit_time ? $ticket->exit_time->format('d-m-Y H:i') : '-' }}</td>
            <td>
              @if($ticket->total_hours)
                {{ $ticket->total_hours }} hrs
              @else
                -
              @endif
            </td>
            <td>
              @if($ticket->total_amount)
                <strong>{{ number_format($ticket->total_amount, 2) }} Tk</strong>
              @else
                -
              @endif
            </td>
            <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
            <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="11" class="text-center">No parking tickets found matching the criteria.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
      {{ $parking_tickets->appends(request()->except('page'))->links() }}
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
  $('#parkingReportTable').DataTable({
    "order": [[0, "desc"]],
    "pageLength": 25,
    "lengthChange": false,
    "info": false
  });
});
</script>
@endsection
