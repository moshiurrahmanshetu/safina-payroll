@extends('layouts.admin')
@section('title', 'Work Area Create')
@section('content')
<h3 class="page-header">Work Area Create {{link_to_route('work_areas.index','Work Area List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::open(array('route' => array('work_areas.store'),'class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Name *</label>
        {{Form::text('name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Status *</label>
        {{Form::select('status',config('myhelpers.status'),1,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Description</label>
        {{Form::textarea('description',null, array('class' => 'form-control', 'rows'=>'3'))}}
        {!! $errors->first('description', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Create Work Area
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection

@section('script')

@endsection
