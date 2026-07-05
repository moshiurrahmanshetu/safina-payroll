@extends('layouts.admin')
@section('title', 'Create Gear Item')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-plus-circle mr-2"></i>Create Gear Item</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('gear_items.index') }}">Gear Items</a></li>
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
        <h3 class="card-title"><i class="fa fa-tshirt mr-2"></i>New Gear Item</h3>
      </div>
      {{ Form::open(['route' => 'gear_items.store', 'method' => 'POST']) }}
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
            <i class="fa fa-tshirt mr-1 text-primary"></i>Gear Name
          </label>
          {{ Form::text('name', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'e.g., T-Shirt, Tube, Towel']) }}
        </div>

        <div class="form-group">
          <label class="font-weight-bold">
            <i class="fa fa-boxes mr-1 text-success"></i>Total Stock
          </label>
          {{ Form::number('total_stock', 0, ['class' => 'form-control', 'required' => true, 'min' => 0]) }}
          <small class="form-text text-muted">Available stock will be set equal to total stock initially</small>
        </div>
      </div>

      <div class="card-footer bg-light">
        <button type="submit" class="btn btn-primary">
          <i class="fa fa-save mr-2"></i>Save Gear
        </button>
        <a href="{{ route('gear_items.index') }}" class="btn btn-danger ml-2">
          <i class="fa fa-times mr-2"></i>Cancel
        </a>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

@endsection
