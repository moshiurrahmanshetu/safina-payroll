@extends('layouts.admin')
@section('title', 'Update Purpose')
@section('content')
<h1 class="page-header">Update Purpose {{link_to_route('purpose.create','Purpose List',[],array('class'=>'btn btn-success pull-right'))}} </h1>

{{ Form::model($purposes,array('route' => array('purpose.update',$purposes->id),'class'=>'form-horizontal','method'=>'PUT')) }}
<div class="row">
	<div class="form-group">
		<div class="col-md-6">
			<label for="accountno" class="control-label">Purpose Type <sup>*</sup></label>
			{{Form::select('purpose_type',config('myhelpers.purpose_type'),null,array('class' => 'form-control', 'required'=>'required'))}}
			{!! $errors->first('purpose_type', '<p class="text-danger">:message</p>' ) !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<label for="accountno" class="control-label">Purpose Name <sup>*</sup></label>
			{{Form::text('name',null,array('class' => 'form-control', 'required'=>'required'))}}
			{!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				Update Purpose
			</button>
		</div>
	</div>
</div>
{{ Form::close() }}

@endsection