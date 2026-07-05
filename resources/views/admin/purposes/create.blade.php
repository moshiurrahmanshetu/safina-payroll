@extends('layouts.admin')
@section('title', 'Create Purpose')
@section('content')
<h1 class="page-header">Create Purpose </h1>
{{ Form::model(Request::old(),array('route' => array('purpose.store'),'class'=>'form-horizontal')) }}
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
				Create Purpose
			</button>
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>SL NO</th>
						<th>Purpose Type</th>
						<th>Purpose Name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@php $id=1; @endphp
					@foreach ($purposes as $data)
					<tr>
						<td>{{ $id}}</td>
						<td>{{ config('myhelpers.purpose_type')[$data->purpose_type] }}</td>
						<td>{{ $data->name }}</td>
						<td>{!! HTML::decode(link_to_route('purpose.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!} </td>
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