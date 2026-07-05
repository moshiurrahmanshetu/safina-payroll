@extends('layouts.admin')
@section('title', 'Vehicle List')
@section('content')
<h3 class="page-header">Vehicle List {{link_to_route('vehicles.create','Create Vehicle',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="card">
      <div class="card-body">
        <table class="table table-striped table-bordered" id="vehiclesTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Slot Price (08:00-18:00)</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($vehicles as $vehicle)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td><strong>{{ $vehicle->name }}</strong></td>
              <td>{{ number_format($vehicle->base_price ?? $vehicle->hourly_rate, 2) }} Tk</td>
              <td>
                @if($vehicle->status == 'active')
                  <span class="badge badge-success">Active</span>
                @else
                  <span class="badge badge-secondary">Inactive</span>
                @endif
              </td>
              <td>{{ $vehicle->created_at->format('d-m-Y') }}</td>
              <td>
                {{ link_to_route('vehicles.edit', 'Edit', [$vehicle->id], ['class' => 'btn btn-sm btn-info']) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
  $('#vehiclesTable').DataTable({
    "order": [[0, "asc"]],
    "pageLength": 25
  });
});
</script>
@endsection
