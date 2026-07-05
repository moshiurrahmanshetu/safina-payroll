@extends('layouts.admin')
@section('title', 'Create Roles')
@section('content')

<h1 class="page-header">Create Roles {{link_to_route('roles.index','List All',null,array('class'=>'btn btn-success pull-right'))}}</h1>
{{ Form::model(Request::old(),array('route' => array('roles.store'),'class'=>'form-horizontal')) }}  
<div class="row">		
	<div class="form-group">
		<label class="control-label col-sm-6">Role Name <sup>*</sup> :</label>
		<div class="col-md-6">
			<input type="text" name="name" class="form-control" required="required">
			{!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-6">Role Description : <sup>(maximum length should be 600)</sup></label>
		<div class="col-md-6">
			<textarea name="description" class="form-control" placeholder="maximum length should be 600"></textarea>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<label class="control-label col-sm-6">Status :</label>
			<div class="col-md-6">
				{{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control')) }}
			</div>
		</div>

	</div>

	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				Create Role
			</button>
		</div>
	</div>
</div>

<?php $controllersArray=array_chunk($permission,3,true);?>

<h3> Permission : </h3>
<div><strong>Check/Uncheck All</strong> <input id="checkoruncheck" type="checkbox"> </div>
@foreach ($controllersArray as $elements)
<div class="row">						
	@foreach ($elements as $key=>$elements)	
	<div class="col-md-4">			
		<div class="checkbox controller">
			<label><input name="{{ $key }}" class="parent_{{ $key }}" type="checkbox" checked="true" value="{{ array_search($key, $parents) }}" onChange="permission_select_deselect_child(this)"> <strong>{{ $key }} </strong></label>
		</div>
		<div class="action-wraper">
			@foreach ($elements as $key2=>$element)
			<div class="checkbox actions" style="margin-left:20px;">
				<label><input name="{{ $key2 }}" class="{{ $key }}" type="checkbox" checked="true" value="{{ $key2 }}" onChange="permission_select_parent('{{ $key }}')"> {{ $element }}</label>
			</div>
			@endforeach
		</div>
	</div>
	@endforeach
</div>
@endforeach

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="form-group">
			<button type="submit" class="btn btn-success">Submit</button>
		</div>
	</div>	
</div>
{{ Form::close() }}
@endsection 

@section('script')
<script>
	$(document).ready(function(e){
		$("#checkoruncheck").change(function () {
			$("input:checkbox").prop('checked', $(this).prop("checked"));
		});
	});
</script>
@endsection