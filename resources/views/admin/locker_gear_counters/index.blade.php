@extends('layouts.admin')
@section('title', 'Locker & Gear Counters')
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-building mr-2"></i>Locker & Gear Counters @if($counters) ({{count($counters)}}) @endif</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Locker & Gear Counters</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main Card -->
<div class="card card-outline card-primary shadow">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>Counter List</h3>
    <div class="card-tools">
      @if(checkMenuActive('LockerGearCounterController@create', $menu_list))
      <a href="{{ route('locker_gear_counters.create') }}" class="btn btn-success btn-sm">
        <i class="fa fa-plus-circle mr-1"></i> Create Counter
      </a>
      @endif
    </div>
  </div>

  <div class="card-body p-0">
    @if(session('flash_success'))
    <div class="alert alert-success m-3">
      <i class="fa fa-check-circle mr-2"></i>{{ session('flash_success') }}
    </div>
    @endif

    @if(session('flash_error'))
    <div class="alert alert-danger m-3">
      <i class="fa fa-exclamation-circle mr-2"></i>{{ session('flash_error') }}
    </div>
    @endif

    <!-- Table -->
    <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Assigned Users</th>
                <th>Assigned Tickets</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($counters as $counter)
              <tr>
                <td>{{ $counter->id }}</td>
                <td><strong>{{ $counter->name }}</strong></td>
                <td>
                  @if($counter->status == 'active')
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
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
              {{ $counter->locker_gear_tickets_count }}
            </td>

                <td>{{ $counter->created_at->format('d M Y') }}</td>
                <td>
                  <div class="btn-group">
                    @if(checkMenuActive('LockerGearCounterController@edit', $menu_list))
                    <a href="{{ route('locker_gear_counters.edit', $counter->id) }}" class="btn btn-sm btn-info" title="Edit">
                      <i class="fa fa-edit"></i>
                    </a>
                    @endif
                    @if(checkMenuActive('LockerGearCounterController@assign_users', $menu_list))
                    <a href="{{ route('locker_gear_counters.assign_users', $counter->id) }}" class="btn btn-sm btn-primary" title="Assign Users">
                      <i class="fa fa-users"></i>
                    </a>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  <i class="fa fa-info-circle mr-2"></i>No counters found.
                  <a href="{{ route('locker_gear_counters.create') }}" class="ml-2">Create one now</a>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          {{ $counters->links() }}
        </div>
      </div>

</div>
@endsection
