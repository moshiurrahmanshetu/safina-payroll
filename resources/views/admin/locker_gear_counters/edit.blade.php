@extends('layouts.admin')
@section('title', 'Edit Locker & Gear Counter')
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Edit Counter</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('locker_gear_counters.index') }}">Counters</a></li>
          <li class="breadcrumb-item active">Edit</li>
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
        <div class="col-md-6">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-edit mr-2"></i>Edit Counter: {{ $counter->name }}</h3>
            </div>
            <div class="card-body">
              {{ Form::model($counter, ['route' => ['locker_gear_counters.update', $counter->id], 'method' => 'PUT']) }}
              <div class="form-group">
                <label>Counter Name <span class="text-danger">*</span></label>
                {{ Form::text('name', null, ['class' => 'form-control', 'required' => true]) }}
              </div>
              <div class="form-group">
                <label>Status</label>
                {{ Form::select('status', ['active' => 'Active', 'inactive' => 'Inactive'], null, ['class' => 'form-control']) }}
              </div>
              <div class="form-group">
                <label><i class="fa fa-users mr-1"></i>Assign Users</label>
                <div class="card border">
                  <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                    @foreach($users as $user)
                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="user_{{ $user->id }}" name="users[]" value="{{ $user->id }}" {{ in_array($user->id, $selectedUsers) ? 'checked' : '' }}>
                      <label class="custom-control-label" for="user_{{ $user->id }}">
                        <strong>{{ $user->name }}</strong> <small class="text-muted">({{ $user->email }})</small>
                      </label>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-success">
                  <i class="fa fa-save mr-1"></i> Update Counter
                </button>
                <a href="{{ route('locker_gear_counters.index') }}" class="btn btn-danger">
                  <i class="fa fa-times mr-1"></i> Cancel
                </a>
              </div>
              {{ Form::close() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
