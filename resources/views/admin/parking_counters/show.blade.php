@extends('layouts.admin')

@section('content')
<h3 class="page-header">Parking Counter Details: {{ $counter->name }} {{link_to_route('parking_counters.index','Parking Counter List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <table class="table table-bordered">
          <tr>
            <th style="width: 150px;">ID</th>
            <td>{{ $counter->id }}</td>
          </tr>
          <tr>
            <th>Counter Name</th>
            <td>{{ $counter->name }}</td>
          </tr>
          <tr>
            <th>Description</th>
            <td>{{ $counter->description ?? '-' }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>
              @if($counter->status == 1)
                <span class="badge badge-success">Active</span>
              @else
                <span class="badge badge-danger">Inactive</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ $counter->created_at->format('d-m-Y H:i:s') }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ $counter->updated_at->format('d-m-Y H:i:s') }}</td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <h5>Assigned Users ({{ $counter->users->count() }})</h5>
        @if($counter->users->count() > 0)
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
              </tr>
            </thead>
            <tbody>
              @foreach($counter->users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <p class="text-muted">No users assigned to this counter.</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
