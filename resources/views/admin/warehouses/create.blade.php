@extends('layouts.admin')
@section('title', 'Create Warehouse')
@section('content')
<h1 class="page-header">Create Warehouse </h1>
{{ Form::model(Request::old(),array('route' => array('warehouse.store'),'class'=>'form-horizontal')) }}
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
				Create Warehouse
			</button>
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-8 col-md-offset-1">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>SL NO</th>
						<th>Warehouse</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@php $id=1; @endphp
					@foreach ($warehouses as $data)
					<tr>
						<td>{{ $id}}</td>
						<td>{{ $data->name }}</td>
						<td>{!! HTML::decode(link_to_route('warehouse.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!} </td>
					</tr>
					@php $id++; @endphp
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
{{ Form::close() }}

@endsection