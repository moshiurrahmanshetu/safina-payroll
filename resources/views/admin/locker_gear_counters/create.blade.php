@extends('layouts.admin')
@section('title', 'Create Locker & Gear Counter')
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Create Locker & Gear Counter</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('locker_gear_counters.index') }}">Counters</a></li>
          <li class="breadcrumb-item active">Create</li>
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
          <div class="card card-outline card-success">
            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-plus-circle mr-2"></i>Counter Details</h3>
            </div>
            <div class="card-body">
              {{ Form::open(['route' => 'locker_gear_counters.store', 'method' => 'POST']) }}
              
              <div class="form-group">
                <label>Counter Name <span class="text-danger">*</span></label>
                {{ Form::text('name', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'e.g., Locker Counter 1']) }}
              </div>

              <div class="form-group">
                <label>Status</label>
                {{ Form::select('status', ['active' => 'Active', 'inactive' => 'Inactive'], 'active', ['class' => 'form-control']) }}
              </div>

              <div class="form-group">
                <label><i class="fa fa-users mr-1"></i>Assign Users</label>
                <div class="card border">
                  <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                    @foreach($users as $user)
                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="user_{{ $user->id }}" name="users[]" value="{{ $user->id }}">
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
                  <i class="fa fa-save mr-1"></i> Create Counter
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
