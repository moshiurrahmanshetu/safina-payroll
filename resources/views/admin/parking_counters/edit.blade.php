@extends('layouts.admin')

@section('content')
<h3 class="page-header">Edit Parking Counter {{link_to_route('parking_counters.index','Parking Counter List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model($counter, ['route' => ['parking_counters.update', $counter->id], 'method' => 'PUT', 'class' => 'form-horizontal']) }}
@csrf
<div class="row">

  <div class="col-md-6">
    <div class="form-group">
      <label class="control-label">Counter Name *</label>
      {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'e.g. Front Desk, Counter 1']) }}
      {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label class="control-label">Description</label>
      {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Optional description']) }}
      {!! $errors->first('description', '<p class="text-danger">:message</p>') !!}
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label class="control-label">Status *</label>
      {{ Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control', 'required' => 'required']) }}
      {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
    </div>
  </div>

  <div class="col-md-12">
    <div class="form-group">
      <label class="control-label">Assign Users</label>
      <div style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #f9f9f9;">
        @foreach($users as $id => $name)
          <div class="checkbox" style="margin: 8px 0;">
            <label style="cursor: pointer; user-select: none;">
              <input type="checkbox" name="users[]" value="{{ $id }}" {{ in_array($id, $assignedUserIds) ? 'checked' : '' }}> {{ $name }}
            </label>
          </div>
        @endforeach
        @if(count($users) === 0)
          <p class="text-muted">No active users available</p>
        @endif
      </div>
      <small class="text-muted">Check the users to assign to this counter</small>
    </div>
  </div>

  <div class="col-md-12">
    <div class="form-group">
      {{ Form::submit('Update', ['class' => 'btn btn-success']) }}
      {{ link_to_route('parking_counters.index', 'Cancel', [], ['class' => 'btn btn-default']) }}
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
