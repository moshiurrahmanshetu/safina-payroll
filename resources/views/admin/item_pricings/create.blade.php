@extends('layouts.admin')
@section('title', 'Create Item Pricing')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-plus-circle mr-2"></i>Create Item Pricing</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('item_pricings.index') }}">Pricing</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-outline card-primary shadow">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-tags mr-2"></i>New Pricing</h3>
      </div>
      {{ Form::open(['route' => 'item_pricings.store', 'method' => 'POST']) }}
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

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-cube mr-1 text-primary"></i>Item Type
              </label>
              {{ Form::select('item_type', ['locker' => 'Locker', 'gear' => 'Gear'], null, ['class' => 'form-control', 'required' => true, 'id' => 'item_type', 'placeholder' => '-- Select Type --']) }}
            </div>
          </div>
          <div class="col-md-6" id="item_select_container">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-list mr-1 text-info"></i>Item
              </label>
              <select name="item_id" id="item_id" class="form-control">
                <option value="">-- Select Item --</option>
              </select>
            </div>
          </div>
          <div class="col-md-6" id="locker_global_message" style="display: none;">
            <div class="alert alert-info mb-0 mt-2">
              <i class="fa fa-info-circle mr-2"></i>
              <strong>Global Locker Pricing</strong><br>
              <small>This pricing will apply to ALL lockers</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-clock mr-1 text-warning"></i>Duration (minutes)
              </label>
              {{ Form::number('duration_minutes', 60, ['class' => 'form-control', 'required' => true, 'min' => 1]) }}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-money-bill mr-1 text-success"></i>Base Price (Tk)
              </label>
              {{ Form::number('base_price', 0, ['class' => 'form-control', 'required' => true, 'min' => 0, 'step' => '0.01']) }}
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-plus-circle mr-1 text-danger"></i>Extra Unit (minutes)
              </label>
              {{ Form::number('extra_unit_minutes', 30, ['class' => 'form-control', 'required' => true, 'min' => 1]) }}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="font-weight-bold">
                <i class="fa fa-tag mr-1 text-secondary"></i>Extra Unit Price (Tk)
              </label>
              {{ Form::number('extra_unit_price', 0, ['class' => 'form-control', 'required' => true, 'min' => 0, 'step' => '0.01']) }}
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer bg-light">
        <button type="submit" class="btn btn-primary">
          <i class="fa fa-save mr-2"></i>Save Pricing
        </button>
        <a href="{{ route('item_pricings.index') }}" class="btn btn-danger ml-2">
          <i class="fa fa-times mr-2"></i>Cancel
        </a>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

<script>
  // Dynamic item dropdown based on type
  var lockers = @json($lockers);
  var gears = @json($gears);

  document.getElementById('item_type').addEventListener('change', function() {
    var itemSelectContainer = document.getElementById('item_select_container');
    var itemSelect = document.getElementById('item_id');
    var lockerMessage = document.getElementById('locker_global_message');

    if (this.value === 'locker') {
      // For locker: hide item dropdown, show global message
      itemSelectContainer.style.display = 'none';
      itemSelect.removeAttribute('required');
      itemSelect.value = ''; // Clear selection
      lockerMessage.style.display = 'block';
    } else if (this.value === 'gear') {
      // For gear: show item dropdown, hide global message
      itemSelectContainer.style.display = 'block';
      itemSelect.setAttribute('required', 'required');
      lockerMessage.style.display = 'none';

      // Populate gear items
      itemSelect.innerHTML = '<option value="">-- Select Item --</option>';
      for (var id in gears) {
        var option = document.createElement('option');
        option.value = id;
        option.textContent = gears[id];
        itemSelect.appendChild(option);
      }
    } else {
      // No selection: reset
      itemSelectContainer.style.display = 'block';
      itemSelect.setAttribute('required', 'required');
      itemSelect.innerHTML = '<option value="">-- Select Item --</option>';
      lockerMessage.style.display = 'none';
    }
  });
</script>

@endsection
