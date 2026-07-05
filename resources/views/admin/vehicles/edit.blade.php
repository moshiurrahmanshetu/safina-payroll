@extends('layouts.admin')
@section('title', 'Edit Vehicle')
@section('content')
<h3 class="page-header">Edit Vehicle</h3>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        {{ Form::model($vehicle, ['route' => ['vehicles.update', $vehicle->id], 'method' => 'PUT']) }}

        <div class="form-group">
          <label>Vehicle Name <span class="text-danger">*</span></label>
          {{ Form::text('name', null, ['class' => 'form-control', 'required' => true]) }}
          @if($errors->has('name'))
            <span class="text-danger">{{ $errors->first('name') }}</span>
          @endif
        </div>

        <div class="form-group">
          <label>Slot Price (Tk) <span class="text-danger">*</span></label>
          {{ Form::number('base_price', $vehicle->base_price ?? $vehicle->hourly_rate, ['class' => 'form-control', 'required' => true, 'min' => '0', 'step' => '0.01']) }}
          <small class="text-muted">Price for one slot (08:00 - 18:00 = 10 hours). Overstay charges apply per additional slot.</small>
          @if($errors->has('base_price'))
            <span class="text-danger">{{ $errors->first('base_price') }}</span>
          @endif
        </div>

        <div class="form-group">
          <label>Status <span class="text-danger">*</span></label>
          {{ Form::select('status', ['active' => 'Active', 'inactive' => 'Inactive'], null, ['class' => 'form-control']) }}
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-success">Update Vehicle</button>
          {{ link_to_route('vehicles.index', 'Cancel', [], ['class' => 'btn btn-danger']) }}
        </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
@endsection
