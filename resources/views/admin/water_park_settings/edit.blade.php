@extends('layouts.admin')
@section('title', 'Water Park Settings')
@section('content')

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-cogs mr-2"></i>Water Park Settings</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Settings</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-outline card-primary shadow">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-sliders-h mr-2"></i>Time Configuration</h3>
      </div>

      {{ Form::open(['route' => 'water_park_settings.update', 'method' => 'POST']) }}
      <div class="card-body">
        @if(session('flash_success'))
        <div class="alert alert-success">
          <i class="fa fa-check-circle mr-2"></i>{{ session('flash_success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <!-- Duration Minutes -->
        <div class="form-group row">
          <label class="col-sm-4 col-form-label font-weight-bold">
            <i class="fa fa-clock mr-1 text-primary"></i>Duration (Minutes)
          </label>
          <div class="col-sm-8">
            {{ Form::number('duration_minutes', $settings->duration_minutes, ['class' => 'form-control', 'required' => true, 'min' => 1]) }}
            <small class="form-text text-muted">Standard time included in base price (e.g., 120 for 2 hours)</small>
          </div>
        </div>

        <!-- Price -->
        <div class="form-group row">
          <label class="col-sm-4 col-form-label font-weight-bold">
            <i class="fa fa-money-bill mr-1 text-success"></i>Base Price (Tk)
          </label>
          <div class="col-sm-8">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-success text-white">Tk</span>
              </div>
              {{ Form::number('price', $settings->price, ['class' => 'form-control', 'required' => true, 'min' => 0, 'step' => '0.01']) }}
            </div>
            <small class="form-text text-muted">Fixed price for the standard duration</small>
          </div>
        </div>

        <hr class="my-4">

        <h5 class="text-info mb-3"><i class="fa fa-hourglass-half mr-2"></i>Overtime Configuration</h5>

        <!-- Extra Unit Minutes -->
        <div class="form-group row">
          <label class="col-sm-4 col-form-label font-weight-bold">
            <i class="fa fa-stopwatch mr-1 text-warning"></i>Extra Unit (Minutes)
          </label>
          <div class="col-sm-8">
            {{ Form::number('extra_unit_minutes', $settings->extra_unit_minutes, ['class' => 'form-control', 'required' => true, 'min' => 1]) }}
            <small class="form-text text-muted">Time unit for overtime calculation (e.g., 30 for 30-minute blocks)</small>
          </div>
        </div>

        <!-- Extra Unit Price -->
        <div class="form-group row">
          <label class="col-sm-4 col-form-label font-weight-bold">
            <i class="fa fa-plus-circle mr-1 text-danger"></i>Extra Unit Price (Tk)
          </label>
          <div class="col-sm-8">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-danger text-white">Tk</span>
              </div>
              {{ Form::number('extra_unit_price', $settings->extra_unit_price, ['class' => 'form-control', 'required' => true, 'min' => 0, 'step' => '0.01']) }}
            </div>
            <small class="form-text text-muted">Price per extra time unit</small>
          </div>
        </div>

        <!-- Preview -->
        <div class="alert alert-light border mt-4">
          <h6 class="text-muted"><i class="fa fa-info-circle mr-2"></i>Current Configuration</h6>
          <p class="mb-1">Standard Duration: <strong>{{ $settings->duration_minutes }} minutes</strong> at <strong>{{ number_format($settings->price, 2) }} Tk</strong></p>
          <p class="mb-0">Overtime: <strong>{{ $settings->extra_unit_minutes }} minutes</strong> per unit at <strong>{{ number_format($settings->extra_unit_price, 2) }} Tk</strong></p>
        </div>
      </div>

      <div class="card-footer bg-light">
        <div class="row">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save mr-2"></i>Update Settings
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-danger ml-2">
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
