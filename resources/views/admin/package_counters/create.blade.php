@extends('layouts.admin')
@section('title', 'Create Package Counter')
@section('content')
<h3 class="page-header">Create Package Counter {{link_to_route('package_counters.index','Package Counter List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::open(['route' => 'package_counters.store', 'class' => 'form-horizontal']) }}
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
      <label class="control-label">Status *</label>
      {{ Form::select('status', ['1' => 'Active', '0' => 'Inactive'], '1', ['class' => 'form-control', 'required' => 'required']) }}
      {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <label class="control-label">Assign Users</label>
      <div style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #f9f9f9;">
        @foreach($users as $id => $name)
          <div class="checkbox" style="margin: 8px 0;">
            <label style="cursor: pointer; user-select: none;">
              <input type="checkbox" name="users[]" value="{{ $id }}"> {{ $name }}
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
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <label class="control-label">Allowed Packages</label>
      <div style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #f9f9f9;">
        @foreach($packages as $id => $name)
          <div class="checkbox" style="margin: 8px 0;">
            <label style="cursor: pointer; user-select: none;">
              <input type="checkbox" name="packages[]" value="{{ $id }}"> {{ $name }}
            </label>
          </div>
        @endforeach
        @if(count($packages) === 0)
          <p class="text-muted">No active packages available</p>
        @endif
      </div>
      <small class="text-muted">Check the packages that should be available for booking at this counter</small>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      {{ Form::submit('Create', ['class' => 'btn btn-success']) }}
      {{ link_to_route('package_counters.index', 'Cancel', [], ['class' => 'btn btn-default']) }}
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
