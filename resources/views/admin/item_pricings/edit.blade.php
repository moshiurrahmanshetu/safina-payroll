@extends('layouts.admin')
@section('title', 'Edit Item Pricing')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-edit mr-2"></i>Edit Item Pricing</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('item_pricings.index') }}">Pricing</a></li>
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
        <h3 class="card-title"><i class="fa fa-tags mr-2"></i>Edit Pricing</h3>
      </div>
      {{ Form::open(['route' => ['item_pricings.update', $pricing->id], 'method' => 'PUT']) }}
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

        <div class="alert alert-info">
          <i class="fa fa-info-circle mr-2"></i>
          <strong>Item:</strong>
          @if($pricing->item_type == 'locker' && is_null($pricing->item_id))
            <span class="text-success"><i class="fa fa-globe mr-1"></i>All Lockers (Global Pricing)</span>
          @else
            {{ $pricing->item->name ?? 'N/A' }}
          @endif
          @if($pricing->item_type == 'locker')
            <span class="badge badge-info ml-2">Locker</span>
          @else
            <span class="badge badge-warning ml-2">Gear</span>
          @endif
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-clock mr-1 text-warning"></i>Duration (minutes)
              </label>
              {{ Form::number('duration_minutes', $pricing->duration_minutes, ['class' => 'form-control', 'required' => true, 'min' => 1]) }}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-money-bill mr-1 text-success"></i>Base Price (Tk)
              </label>
              {{ Form::number('base_price', $pricing->base_price, ['class' => 'form-control', 'required' => true, 'min' => 0, 'step' => '0.01']) }}
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-plus-circle mr-1 text-danger"></i>Extra Unit (minutes)
              </label>
              {{ Form::number('extra_unit_minutes', $pricing->extra_unit_minutes, ['class' => 'form-control', 'required' => true, 'min' => 1]) }}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-tag mr-1 text-danger"></i>Extra Unit Price (Tk)
              </label>
              {{ Form::number('extra_unit_price', $pricing->extra_unit_price, ['class' => 'form-control', 'required' => true, 'min' => 0, 'step' => '0.01']) }}
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer bg-light">
        <button type="submit" class="btn btn-primary">
          <i class="fa fa-save mr-2"></i>Update Pricing
        </button>
        <a href="{{ route('item_pricings.index') }}" class="btn btn-danger ml-2">
          <i class="fa fa-times mr-2"></i>Cancel
        </a>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

@endsection
