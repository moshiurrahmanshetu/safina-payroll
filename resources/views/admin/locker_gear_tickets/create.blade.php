@extends('layouts.admin')
@section('title', 'Create Locker & Gear Ticket')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h3 class="m-0 text-dark"><i class="fa fa-plus-circle mr-2"></i>Create Locker & Gear Ticket</h3>
        <h5 class="text-success">( Counter: {{ $counterName }})</h5>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('locker_gear_tickets.index') }}">Tickets</a></li>
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
        <h3 class="card-title"><i class="fa fa-ticket-alt mr-2"></i>New Rental Ticket</h3>
      </div>
      {{ Form::open(['route' => 'locker_gear_tickets.store', 'method' => 'POST']) }}
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

        @if(session('flash_error'))
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-circle mr-2"></i>{{ session('flash_error') }}
        </div>
        @endif

        <!-- Locker Selection (Optional) -->
        <div class="form-group">
          <label class="font-weight-bold">
            <i class="fa fa-lock mr-1 text-primary"></i>Locker 
          </label>
          @if(count($lockers) > 0)
            {{ Form::select('locker_id', $lockers, null, ['class' => 'form-control', 'placeholder' => '-- Select Locker --']) }}
            <small class="form-text text-muted">Select a locker or gear items (at least one required)</small>
          @else
            <div class="alert alert-light border">
              <i class="fa fa-info-circle mr-2 text-muted"></i>No lockers available. You can still create gear-only tickets.
            </div>
          @endif
        </div>

        <hr class="my-4">

        <!-- Gear Selection -->
        <div class="form-group">
          <label class="font-weight-bold">
            <i class="fa fa-tshirt mr-1 text-success"></i>Select Gear Items
          </label>
          
          @if(count($gears) > 0)
            <div id="gear-container">
              <div class="gear-row mb-2">
                <div class="row">
                  <div class="col-7">
                    <select name="gear_items[0][id]" class="form-control gear-select">
                      <option value="">-- Select Gear --</option>
                      @foreach($gears as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-3">
                    <input type="number" name="gear_items[0][quantity]" class="form-control" min="1" value="1" placeholder="Qty">
                  </div>
                  <div class="col-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-gear" style="display:none;">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-gear">
              <i class="fa fa-plus mr-1"></i>Add Another Gear
            </button>
          @else
            <div class="alert alert-info">
              <i class="fa fa-info-circle mr-2"></i>No gear items in stock.
            </div>
          @endif
        </div>
      </div>

      <div class="card-footer bg-light">
        <button type="submit" class="btn btn-success btn-lg">
          <i class="fa fa-ticket-alt mr-2"></i>CREATE TICKET
        </button>
        <a href="{{ route('locker_gear_tickets.index') }}" class="btn btn-danger ml-2">
          <i class="fa fa-times mr-2"></i>Cancel
        </a>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

<script>
  let gearIndex = 1;
  const gears = @json($gears);

  document.getElementById('add-gear')?.addEventListener('click', function() {
    const container = document.getElementById('gear-container');
    const newRow = document.createElement('div');
    newRow.className = 'gear-row mb-2';
    
    let options = '<option value="">-- Select Gear --</option>';
    for (let id in gears) {
      options += `<option value="${id}">${gears[id]}</option>`;
    }
    
    newRow.innerHTML = `
      <div class="row">
        <div class="col-7">
          <select name="gear_items[${gearIndex}][id]" class="form-control gear-select">
            ${options}
          </select>
        </div>
        <div class="col-3">
          <input type="number" name="gear_items[${gearIndex}][quantity]" class="form-control" min="1" value="1" placeholder="Qty">
        </div>
        <div class="col-2">
          <button type="button" class="btn btn-outline-danger btn-sm remove-gear">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>
    `;
    
    container.appendChild(newRow);
    gearIndex++;
    
    // Show all remove buttons
    document.querySelectorAll('.remove-gear').forEach(btn => btn.style.display = 'inline-block');
  });

  // Remove gear row
  document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-gear')) {
      e.target.closest('.gear-row').remove();
    }
  });
</script>

@endsection
