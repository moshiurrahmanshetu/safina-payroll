@extends('layouts.admin')
@section('title', 'Assign Users to Counter')
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Assign Users to Counter</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('locker_gear_counters.index') }}">Counters</a></li>
          <li class="breadcrumb-item active">Assign Users</li>
        </ol>
      </div>
    </div>
  </div>
</div>

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

<section class="content">
  <div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-users mr-2"></i>Assign Users to: {{ $counter->name }}</h3>
            </div>
            <div class="card-body">
              {{ Form::open(['route' => ['locker_gear_counters.update_users', $counter->id], 'method' => 'POST']) }}
              <div class="form-group">
                <label>Select Users</label>
                <div class="card">
                  <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @foreach($users as $user)
                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="user_{{ $user->id }}" name="user_ids[]" value="{{ $user->id }}" {{ in_array($user->id, $assignedUserIds) ? 'checked' : '' }}>
                      <label class="custom-control-label" for="user_{{ $user->id }}">
                        <strong>{{ $user->name }}</strong>
                      </label>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-success">
                  <i class="fa fa-save mr-1"></i> Save Assignments
                </button>
                <a href="{{ route('locker_gear_counters.index') }}" class="btn btn-secondary">
                  <i class="fa fa-times mr-1"></i> Cancel
                </a>
              </div>
              {{ Form::close() }}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-info-circle mr-2"></i>Information</h3>
            </div>
            <div class="card-body">
              <p>Users assigned to this counter can:</p>
              <ul>
                <li>Create tickets for this counter</li>
                <li>View tickets from their assigned counters</li>
                <li>Process check-outs</li>
              </ul>
              <p class="text-muted">Users without an assigned counter cannot create tickets.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
