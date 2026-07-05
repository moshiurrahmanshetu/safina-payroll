@extends('layouts.admin')
@section('title', 'Create Locker')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-plus-circle mr-2"></i>Create Locker</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('locker_items.index') }}">Lockers</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card card-outline card-primary shadow">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-lock mr-2"></i>New Locker</h3>
      </div>
      {{ Form::open(['route' => 'locker_items.store', 'method' => 'POST']) }}
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

        <div class="form-group">
          <label class="font-weight-bold">
            <i class="fa fa-lock mr-1 text-primary"></i>Locker Name
          </label>
          {{ Form::text('name', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'e.g., Locker 1, Locker A']) }}
        </div>

        <div class="form-group">
          <label class="font-weight-bold">
            <i class="fa fa-toggle-on mr-1 text-success"></i>Status
          </label>
          {{ Form::select('status', ['available' => 'Available', 'occupied' => 'Occupied'], 'available', ['class' => 'form-control']) }}
        </div>
      </div>

      <div class="card-footer bg-light">
        <button type="submit" class="btn btn-primary">
          <i class="fa fa-save mr-2"></i>Save Locker
        </button>
        <a href="{{ route('locker_items.index') }}" class="btn btn-danger ml-2">
          <i class="fa fa-times mr-2"></i>Cancel
        </a>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

@endsection
