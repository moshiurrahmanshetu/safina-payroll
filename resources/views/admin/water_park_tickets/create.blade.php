@extends('layouts.admin')
@section('title', 'Create Water Park Ticket')
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h3 class="m-0 text-dark"><i class="fa fa-plus-circle mr-2"></i>Create Water Park Ticket</h3>
        <h5 class="text-success">( Counter: {{$counterName}})</h5>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('water_park_tickets.index') }}">Water Park Tickets</a></li>
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
        <h3 class="card-title"><i class="fa fa-ticket-alt mr-2"></i>New Ticket</h3>
      </div>
      {{ Form::open(['route' => 'water_park_tickets.store', 'method' => 'POST']) }}
      <div class="card-body text-center">
        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <div class="alert alert-info mb-4">
          <h5><i class="fa fa-info-circle mr-2"></i>Auto-Generate Ticket</h5>
          <p class="mb-1">Counter, duration and price will be auto-filled from your settings</p>
        </div>

        <!-- Quantity Field -->
        <div class="form-group row justify-content-center">
          <label class="col-sm-3 col-form-label font-weight-bold">
            <i class="fa fa-layer-group mr-1 text-primary"></i>Quantity
          </label>
          <div class="col-sm-3">
            {{ Form::number('quantity', 1, ['class' => 'form-control form-control-lg text-center', 'min' => 1, 'max' => 50, 'required' => true]) }}
            <small class="form-text text-muted">Max 50 tickets at once</small>
          </div>
        </div>
      </div>

      <div class="card-footer bg-light text-center">
        <button type="submit" class="btn btn-success btn-lg">
          <i class="fa fa-ticket-alt mr-2"></i>CREATE TICKET
        </button>
        <a href="{{ route('water_park_tickets.index') }}" class="btn btn-danger ml-2">
          <i class="fa fa-times mr-2"></i>Cancel
        </a>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

@endsection
