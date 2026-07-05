@extends('layouts.admin')
@section('title', 'Ticket Counter Edit')
@section('content')
<h3 class="page-header">Ticket Counter Edit {{link_to_route('gates.index','Ticket Counter List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model($gate,array('route' => array('gates.update',$gate->id),'method'=>'PUT','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Ticket Counter Name *</label>
        {{Form::text('name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Status</label>
        {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}
      </div>
    </div>
  </div>
  <div class="col-md-6 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Select Users</label>
        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #f9f9f9;">
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
        <small class="text-muted">Check the users to assign to this ticket counter</small>
      </div>
    </div>
  </div>
  <div class="col-md-6 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Allowed Tickets</label>
        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #f9f9f9;">
          @foreach($tickets as $id => $name)
            <div class="checkbox" style="margin: 8px 0;">
              <label style="cursor: pointer; user-select: none;">
                <input type="checkbox" name="tickets[]" value="{{ $id }}" {{ in_array($id, $assignedTicketIds) ? 'checked' : '' }}> {{ $name }}
              </label>
            </div>
          @endforeach
          @if(count($tickets) === 0)
            <p class="text-muted">No active tickets available</p>
          @endif
        </div>
        <small class="text-muted">Check the tickets that can be sold at this Counter</small>
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Update Counter
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
