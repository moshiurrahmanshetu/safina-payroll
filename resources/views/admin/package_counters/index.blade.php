@extends('layouts.admin')
@section('title', 'Package Counters')
@section('content')
<h3 class="page-header">Package Counters @if($counters) ({{count($counters)}}) @endif {{link_to_route('package_counters.create','Create Package Counter',[],array('class'=>'btn btn-success pull-right'))}}</h3>

@if(count($counters) > 0)
  <table class="table table-bordered table-striped table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Assigned Users</th>
        <th>Assigned Packages</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($counters as $counter)
        <tr>
          <td>{{ $counter->id }}</td>
          <td>{{ $counter->name }}</td>
          <td>
            @if($counter->users->count() > 0)
              @foreach($counter->users as $user)
                <span class="badge badge-info">{{ $user->name }}</span>
              @endforeach
            @else
              <span class="text-muted">No users assigned</span>
            @endif
          </td>
          <td>
            @if($counter->packages->count() > 0)
              @foreach($counter->packages as $package)
                <span class="badge badge-success">{{ $package->name }}</span>
              @endforeach
            @else
              <span class="text-muted">No packages assigned</span>
            @endif
          </td>
          <td>
            @if($counter->status == 1)
              <span class="label label-success">Active</span>
            @else
              <span class="label label-danger">Inactive</span>
            @endif
          </td>
          <td>
            {{ link_to_route('package_counters.edit', 'Edit', [$counter->id], ['class' => 'btn btn-sm btn-primary']) }}
            {{ Form::open(['route' => ['package_counters.destroy', $counter->id], 'method' => 'DELETE', 'style' => 'display:inline']) }}
              {{ Form::submit('Delete', ['class' => 'btn btn-sm btn-danger', 'onclick' => 'return confirm("Are you sure?")']) }}
            {{ Form::close() }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@else
  <div class="alert alert-info">No package counters found. {{ link_to_route('package_counters.create', 'Create your first package counter') }}</div>
@endif
@endsection
