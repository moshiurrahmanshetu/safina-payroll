@extends('layouts.admin')
@section('title', 'Edit Water Park Counter')
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-edit mr-2"></i>Edit Water Park Counter</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('water_park_counters.index') }}">Counters</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-outline card-primary shadow">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-desktop mr-2"></i>Edit Counter</h3>
      </div>
      {{ Form::open(['route' => ['water_park_counters.update', $counter->id], 'method' => 'PUT']) }}
      <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <!-- Counter Name -->
        <div class="form-group row">
          <label class="col-sm-3 col-form-label font-weight-bold">
            <i class="fa fa-desktop mr-1 text-primary"></i>Counter Name
          </label>
          <div class="col-sm-9">
            {{ Form::text('name', $counter->name, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Enter counter name']) }}
          </div>
        </div>

        <!-- Status -->
        <div class="form-group row">
          <label class="col-sm-3 col-form-label font-weight-bold">
            <i class="fa fa-toggle-on mr-1 text-success"></i>Status
          </label>
          <div class="col-sm-9">
            {{ Form::select('status', ['1' => 'Active', '0' => 'Inactive'], $counter->status, ['class' => 'form-control']) }}
          </div>
        </div>

        <!-- Assign Users -->
        <div class="form-group row">
          <label class="col-sm-3 col-form-label font-weight-bold">
            <i class="fa fa-users mr-1 text-info"></i>Assign Users
          </label>
          <div class="col-sm-9">
            {{ Form::select('users[]', $users, $assignedUserIds, ['class' => 'form-control', 'multiple' => true, 'size' => 5]) }}
            <small class="form-text text-muted">Hold Ctrl to select multiple users</small>
          </div>
        </div>
      </div>

      <div class="card-footer bg-light">
        <div class="row">
          <div class="col-sm-9 offset-sm-3">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save mr-2"></i>Update Counter
            </button>
            <a href="{{ route('water_park_counters.index') }}" class="btn btn-danger ml-2">
              <i class="fa fa-times mr-2"></i>Cancel
            </a>
          </div>
        </div>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

@endsection
