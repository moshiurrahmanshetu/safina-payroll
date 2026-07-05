@extends('layouts.admin')
@section('title', 'Package Reports')
@section('content')
<h3 class="page-header">
  Package Reports
</h3>

<!-- Filter Panel -->
<div class="panel panel-default">
  <div class="panel-heading">
    <i class="nav-icon icon-magnifier"></i> Filter Options
    <span class="pull-right">
      <a href="{{ route('package_reports.index') }}" class="btn btn-xs btn-danger">Reset</a>
    </span>
  </div>
  <div class="panel-body">
    <form method="GET" action="{{ route('package_reports.generate') }}" class="form-horizontal">
      <div class="row">
        <!-- From Date -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">From Date</label>
            <input type="text" name="from_date" class="form-control datepicker" placeholder="DD-MM-YYYY"
                   value="{{ $fromDate ?? request('from_date', date('01-m-Y')) }}">
          </div>
        </div>

        <!-- To Date -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">To Date</label>
            <input type="text" name="to_date" class="form-control datepicker" placeholder="DD-MM-YYYY"
                   value="{{ $toDate ?? request('to_date', date('d-m-Y')) }}">
          </div>
        </div>

        <!-- Package Filter -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Package</label>
            <select name="package_id" class="form-control">
              <option value="">All Packages</option>
              @foreach($packages as $id => $name)
                <option value="{{ $id }}" {{ (isset($packageId) && $packageId == $id) || request('package_id') == $id ? 'selected' : '' }}>
                  {{ $name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Counter Filter -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Package Counter</label>
            <select name="counter_id" class="form-control">
              <option value="">All Counters</option>
              @foreach($counters as $id => $name)
                <option value="{{ $id }}" {{ (isset($counterId) && $counterId == $id) || request('counter_id') == $id ? 'selected' : '' }}>
                  {{ $name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- User Filter -->
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">User</label>
            <select name="user_id" class="form-control">
              <option value="">All Users</option>
              @foreach($users as $id => $name)
                <option value="{{ $id }}" {{ (isset($userId) && $userId == $id) || request('user_id') == $id ? 'selected' : '' }}>
                  {{ $name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <div>
              <button type="submit" class="btn btn-primary">
                <i class="nav-icon icon-magnifier"></i> Generate Report
              </button>
              <button type="submit" name="print" value="1" class="btn btn-info" formaction="{{ route('package_reports.print') }}" formtarget="_blank">
                <i class="nav-icon icon-printer"></i> Print
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

@if(isset($bookings))
<!-- Summary Cards -->
<div class="row">
  <div class="col-md-3">
    <div class="panel panel-info">
      <div class="panel-heading text-center">Total Bookings</div>
      <div class="panel-body text-center">
        <h2>{{ $totalBookings }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-success">
      <div class="panel-heading text-center">Total Revenue</div>
      <div class="panel-body text-center">
        <h2>৳{{ number_format($totalRevenue, 2) }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-primary">
      <div class="panel-heading text-center">Base Amount</div>
      <div class="panel-body text-center">
        <h3>৳{{ number_format($totalBaseAmount, 2) }}</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-warning">
      <div class="panel-heading text-center">Extra Amount</div>
      <div class="panel-body text-center">
        <h3>৳{{ number_format($totalExtraAmount, 2) }}</h3>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Package-wise Summary -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Package-wise Summary</div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Package</th>
              <th>Bookings</th>
              <th class="text-right">Revenue</th>
            </tr>
          </thead>
          <tbody>
            @forelse($packageSummary as $name => $data)
            <tr>
              <td>{{ $name }}</td>
              <td>{{ $data['count'] }}</td>
              <td class="text-right">৳{{ number_format($data['revenue'], 2) }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted">No data</td>
            </tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr class="success">
              <th>Total</th>
              <th>{{ $totalBookings }}</th>
              <th class="text-right">৳{{ number_format($totalRevenue, 2) }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <!-- Counter-wise Summary -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Counter-wise Summary</div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Counter</th>
              <th>Bookings</th>
              <th class="text-right">Revenue</th>
            </tr>
          </thead>
          <tbody>
            @forelse($counterSummary as $name => $data)
            <tr>
              <td>{{ $name }}</td>
              <td>{{ $data['count'] }}</td>
              <td class="text-right">৳{{ number_format($data['revenue'], 2) }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted">No data</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- User-wise Summary -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">User-wise Summary</div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>User</th>
              <th>Bookings</th>
              <th class="text-right">Revenue</th>
            </tr>
          </thead>
          <tbody>
            @forelse($userSummary as $name => $data)
            <tr>
              <td>{{ $name }}</td>
              <td>{{ $data['count'] }}</td>
              <td class="text-right">৳{{ number_format($data['revenue'], 2) }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted">No data</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Booking Details -->
<div class="panel panel-default">
  <div class="panel-heading">Booking Details</div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Package</th>
            <th>Qty</th>
            <th>Total Person</th>
            <th class="text-right">Base</th>
            <th class="text-right">Extra</th>
            <th class="text-right">Final</th>
            <th>Package Counter</th>
            <th>Created By</th>
          </tr>
        </thead>
        <tbody>
          @php $i = 1; @endphp
          @forelse($bookings as $booking)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ date('d-m-Y', strtotime($booking->date)) }}</td>
            <td>{{ $booking->package ? $booking->package->name : 'N/A' }}</td>
            <td>{{ $booking->quantity }}</td>
            <td>{{ $booking->total_person }}</td>
            <td class="text-right">৳{{ number_format($booking->base_amount, 2) }}</td>
            <td class="text-right">৳{{ number_format($booking->extra_amount, 2) }}</td>
            <td class="text-right"><strong>৳{{ number_format($booking->final_amount, 2) }}</strong></td>
            <td>{{ $booking->packageCounter ? $booking->packageCounter->name : '-' }}</td>
            <td>{{ $booking->creator ? $booking->creator->name : '-' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center text-muted">No bookings found for selected filters</td>
          </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr class="success">
            <th colspan="5" class="text-right">Total:</th>
            <th class="text-right">৳{{ number_format($totalBaseAmount, 2) }}</th>
            <th class="text-right">৳{{ number_format($totalExtraAmount, 2) }}</th>
            <th class="text-right">৳{{ number_format($totalRevenue, 2) }}</th>
            <th colspan="2"></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endif

@endsection

@section('script')
<script>
$(document).ready(function(){
  // Initialize datepickers
  $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    todayHighlight: true
  });
});
</script>
@endsection
