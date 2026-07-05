@extends('layouts.admin')
@section('title', 'User Report')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-users mr-2"></i>User Performance Report</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">User Report</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Date Filter -->
<div class="card card-outline card-primary mb-4">
  <div class="card-body">
    {{ Form::open(['route' => 'locker_gear_reports.user_report', 'method' => 'GET', 'class' => 'form-inline']) }}
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

<!-- Report Table -->
<div class="card card-outline card-primary">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>User Performance Summary</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>User</th>
            <th>Total Tickets</th>
            <th>Total Revenue</th>
            <th>Average per Ticket</th>
          </tr>
        </thead>
        <tbody>
          @forelse($report as $row)
          <tr>
            <td><strong>{{ $row->creator->name ?? 'Unknown' }}</strong></td>
            <td>{{ $row->total_tickets }}</td>
            <td><strong class="text-success">{{ number_format($row->total_revenue, 2) }} Tk</strong></td>
            <td>{{ $row->total_tickets > 0 ? number_format($row->total_revenue / $row->total_tickets, 2) : '0.00' }} Tk</td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center text-muted py-4">No data available for selected period</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
