@extends('layouts.admin')
@section('title', 'Water Park Time Ranges')
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-clock mr-2"></i>Water Park Time Ranges</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Time Ranges</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card card-outline card-primary shadow">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-list mr-2"></i>Available Time Packages</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Extra Unit (Minutes)</th>
            <th>Extra Unit Price</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($timeRanges as $range)
          <tr>
            <td><strong>{{ $range->name }}</strong></td>
            <td>{{ $range->duration_minutes }} minutes</td>
            <td>{{ number_format($range->price, 2) }} Tk</td>
            <td>{{ $range->extra_unit_minutes }} minutes</td>
            <td>{{ number_format($range->extra_unit_price, 2) }} Tk</td>
            <td>
              @if($range->status)
                <span class="badge badge-success">Active</span>
              @else
                <span class="badge badge-danger">Inactive</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
              <p>No time ranges found.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
