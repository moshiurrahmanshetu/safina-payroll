@extends('layouts.admin')
@section('title', 'Stock Report')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-warehouse mr-2"></i>Stock Report</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Stock Report</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Locker Stats -->
<div class="row">
  <div class="col-md-4">
    <div class="card card-outline card-info">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-lock mr-2"></i>Locker Statistics</h3>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-4">
            <div class="small-box bg-light border">
              <div class="inner">
                <h4>{{ $lockerStats['total'] }}</h4>
                <small>Total</small>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="small-box bg-success border">
              <div class="inner">
                <h4>{{ $lockerStats['available'] }}</h4>
                <small>Available</small>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="small-box bg-danger border">
              <div class="inner">
                <h4>{{ $lockerStats['occupied'] }}</h4>
                <small>Occupied</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card card-outline card-warning">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-tshirt mr-2"></i>Gear Statistics</h3>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-3">
            <div class="small-box bg-light border">
              <div class="inner">
                <h4>{{ $gearStats['total_items'] }}</h4>
                <small>Items</small>
              </div>
            </div>
          </div>
          <div class="col-3">
            <div class="small-box bg-info border">
              <div class="inner">
                <h4>{{ $gearStats['total_stock'] }}</h4>
                <small>Total Stock</small>
              </div>
            </div>
          </div>
          <div class="col-3">
            <div class="small-box bg-success border">
              <div class="inner">
                <h4>{{ $gearStats['total_available'] }}</h4>
                <small>Available</small>
              </div>
            </div>
          </div>
          <div class="col-3">
            <div class="small-box bg-warning border">
              <div class="inner">
                <h4>{{ $gearStats['total_in_use'] }}</h4>
                <small>In Use</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Gear Details -->
<div class="card card-outline card-primary">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>Gear Stock Details</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Item</th>
            <th>Total Stock</th>
            <th>Available</th>
            <th>In Use</th>
            <th>Stock Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($gearItems as $gear)
          <tr>
            <td><strong>{{ $gear['name'] }}</strong></td>
            <td>{{ $gear['total_stock'] }}</td>
            <td>
              @if($gear['available_stock'] > 0)
                <span class="badge badge-success">{{ $gear['available_stock'] }}</span>
              @else
                <span class="badge badge-danger">{{ $gear['available_stock'] }}</span>
              @endif
            </td>
            <td>{{ $gear['in_use'] }}</td>
            <td>
              @if($gear['available_stock'] > 5)
                <span class="badge badge-success">Good</span>
              @elseif($gear['available_stock'] > 0)
                <span class="badge badge-warning">Low</span>
              @else
                <span class="badge badge-danger">Out of Stock</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">No gear items found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
