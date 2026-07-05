@extends('layouts.admin')
@section('title', 'Edit Package')
@section('content')
<h3 class="page-header">Edit Package</h3>

<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel panel-default">
      <div class="panel-heading">Package Details</div>
      <div class="panel-body">
        {{ Form::model($package, array('route' => array('packages.update', $package->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}

          <!-- Package Name -->
          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            {{ Form::label('name', 'Package Name *', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              {{ Form::text('name', old('name', $package->name), array('class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter package name')) }}
              @if ($errors->has('name'))
                <span class="help-block">{{ $errors->first('name') }}</span>
              @endif
            </div>
          </div>

          <!-- Base Price -->
          <div class="form-group{{ $errors->has('base_price') ? ' has-error' : '' }}">
            {{ Form::label('base_price', 'Base Price *', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              <div class="input-group">
                <span class="input-group-addon">৳</span>
                {{ Form::number('base_price', old('base_price', $package->base_price), array('class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'min' => '0', 'placeholder' => '0.00')) }}
              </div>
              @if ($errors->has('base_price'))
                <span class="help-block">{{ $errors->first('base_price') }}</span>
              @endif
            </div>
          </div>

          <!-- Default Person -->
          <div class="form-group{{ $errors->has('default_person') ? ' has-error' : '' }}">
            {{ Form::label('default_person', 'Default Person *', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              {{ Form::number('default_person', old('default_person', $package->default_person), array('class' => 'form-control', 'required' => 'required', 'min' => '1', 'placeholder' => 'Number of persons included')) }}
              <small class="text-muted">Number of persons included in base price</small>
              @if ($errors->has('default_person'))
                <span class="help-block">{{ $errors->first('default_person') }}</span>
              @endif
            </div>
          </div>

          <!-- Extra Person Price -->
          <div class="form-group{{ $errors->has('extra_person_price') ? ' has-error' : '' }}">
            {{ Form::label('extra_person_price', 'Extra Person Price *', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              <div class="input-group">
                <span class="input-group-addon">৳</span>
                {{ Form::number('extra_person_price', old('extra_person_price', $package->extra_person_price), array('class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'min' => '0', 'placeholder' => 'Price per extra person')) }}
              </div>
              <small class="text-muted">Price for each additional person beyond default</small>
              @if ($errors->has('extra_person_price'))
                <span class="help-block">{{ $errors->first('extra_person_price') }}</span>
              @endif
            </div>
          </div>

          <!-- Tickets Checkbox List -->
          <div class="form-group{{ $errors->has('tickets') ? ' has-error' : '' }}">
            {{ Form::label('tickets', 'Included Tickets *', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              <div class="checkbox-list" style="border: 1px solid #ddd; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto;">
                @foreach($tickets as $ticket_id => $ticket_name)
                  @php
                    $isChecked = (is_array(old('tickets')) && in_array($ticket_id, old('tickets'))) || 
                                 (!old('tickets') && in_array($ticket_id, $selectedTickets));
                  @endphp
                  <div class="checkbox" style="margin-top: 5px; margin-bottom: 5px;">
                    <label>
                      <input type="checkbox" name="tickets[]" value="{{ $ticket_id }}" {{ $isChecked ? 'checked' : '' }}>
                      {{ $ticket_name }}
                    </label>
                  </div>
                @endforeach
              </div>
              <small class="text-muted">Select one or more tickets to include in this package</small>
              @if ($errors->has('tickets'))
                <span class="help-block">{{ $errors->first('tickets') }}</span>
              @endif
            </div>
          </div>

          <!-- Status -->
          <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            {{ Form::label('status', 'Status *', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              {{ Form::select('status', array('1' => 'Active', '0' => 'Inactive'), old('status', $package->status), array('class' => 'form-control', 'required' => 'required')) }}
              @if ($errors->has('status'))
                <span class="help-block">{{ $errors->first('status') }}</span>
              @endif
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="form-group">
            <div class="col-md-9 col-md-offset-3">
              {{ Form::submit('Update Package', array('class' => 'btn btn-primary')) }}
              {{ link_to_route('packages.index', 'Cancel', [], array('class' => 'btn btn-danger')) }}
            </div>
          </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

@endsection
