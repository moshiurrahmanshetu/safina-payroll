@extends('layouts.admin')
@section('title', 'Update Warehouse')
@section('content')
<h1 class="page-header">Update Warehouse {{link_to_route('warehouse.create','Warehouse List',[],array('class'=>'btn btn-success pull-right'))}} </h1>

{{ Form::model($warehouse,array('route' => array('warehouse.update',$warehouse->id),'class'=>'form-horizontal','method'=>'PUT')) }}
<div class="row">
	<div class="form-group">
		<div class="col-md-6">
			<label for="accountno" class="control-label">Warehouse Name <sup>*</sup></label>
			{{Form::text('name',null,array('class' => 'form-control', 'required'=>'required'))}}
			{!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				Update Warehouse
			</button>
		</div>
	</div>
</div>
{{ Form::close() }}

@endsection