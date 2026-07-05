@extends('layouts.admin')
@section('title', 'Item Report')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-box mr-2"></i>Item Usage Report</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Item Report</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Date Filter -->
<div class="card card-outline card-primary mb-4">
  <div class="card-body">
    {{ Form::open(['route' => 'locker_gear_reports.item_report', 'method' => 'GET', 'class' => 'form-inline']) }}
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
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>Item Usage Summary</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Item Type</th>
            <th>Item Name</th>
            <th>Base Price</th>
            <th>Total Rentals</th>
            <th>Total Quantity</th>
            <th>Total Revenue</th>
          </tr>
        </thead>
        <tbody>
          @forelse($report as $row)
          <tr>
            <td>
              @if($row['item_type'] == 'locker')
                <span class="badge badge-info"><i class="fa fa-lock mr-1"></i> Locker</span>
              @else
                <span class="badge badge-warning"><i class="fa fa-tshirt mr-1"></i> Gear</span>
              @endif
            </td>
            <td><strong>{{ $row['item_name'] }}</strong></td>
            <td>{{ number_format($row['base_price'], 2) }} Tk</td>
            <td>{{ $row['total_rented'] }}</td>
            <td>{{ $row['total_quantity'] }}</td>
            <td><strong>{{ number_format($row['total_revenue'], 2) }} Tk</strong></td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No data available for selected period</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
