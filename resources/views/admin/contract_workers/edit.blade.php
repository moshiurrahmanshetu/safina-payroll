@extends('layouts.admin')
@section('title', 'Contract Worker Edit')
@section('content')
<h3 class="page-header">Contract Worker Edit {{link_to_route('contract_workers.index','Contract Worker List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model($contract_worker,array('route' => array('contract_workers.update', $contract_worker->id),'method'=>'PUT','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Worker Information</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Contract Worker ID</label>
          <p class="form-control-static"><strong>{{$contract_worker->contract_worker_id}}</strong></p>
        </div>

        @if($contract_worker->user_id)
        <div class="form-group">
          <label class="control-label">Linked User</label>
          <p class="form-control-static">{{$contract_worker->user ? $contract_worker->user->name : 'N/A'}}</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Current Photo</label>
        @if($contract_worker->photo)
          <img src="{{ asset($contract_worker->photo) }}" alt="Current Photo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
        @else
          <span class="text-muted">No Photo</span>
        @endif
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Change Photo</label>
        {{Form::file('photo', array('class' => 'form-control'))}}
        {!! $errors->first('photo', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Full Name *</label>
        {{Form::text('full_name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('full_name', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Mobile *</label>
        {{Form::text('mobile',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('mobile', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Email</label>
        {{Form::email('email',null, array('class' => 'form-control'))}}
        {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">NID</label>
        {{Form::text('nid',null, array('class' => 'form-control'))}}
        {!! $errors->first('nid', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Emergency Contact</label>
        {{Form::text('emergency_contact',null, array('class' => 'form-control'))}}
        {!! $errors->first('emergency_contact', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Present Address</label>
        {{Form::textarea('present_address',null, array('class' => 'form-control', 'rows'=>'3'))}}
        {!! $errors->first('present_address', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Work Area *</label>
        {{Form::select('work_area_id', [''=>'Select Work Area'] + $work_areas, null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('work_area_id', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Contract Title *</label>
        {{Form::text('contract_title',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('contract_title', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Contract Amount *</label>
        {{Form::number('contract_amount',null, array('class' => 'form-control', 'required'=>'required', 'step'=>'0.01'))}}
        {!! $errors->first('contract_amount', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Advance Amount</label>
        {{Form::number('advance_amount',null, array('class' => 'form-control', 'step'=>'0.01'))}}
        {!! $errors->first('advance_amount', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Contract Start Date *</label>
        {{Form::date('contract_start_date',null, array('class' => 'form-control datetimepicker1', 'required'=>'required'))}}
        {!! $errors->first('contract_start_date', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Contract End Date *</label>
        {{Form::date('contract_end_date',null, array('class' => 'form-control datetimepicker1', 'required'=>'required'))}}
        {!! $errors->first('contract_end_date', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Status *</label>
        {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Notes</label>
        {{Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>'3'))}}
        {!! $errors->first('notes', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Update Contract Worker
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection

@section('script')

@endsection
