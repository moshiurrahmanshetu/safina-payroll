@extends('layouts.admin')
@section('title', 'Edit Roles')
@section('content')
<?php $controllersArray=array_chunk($permissions,3,true); ?>

<h1 class="page-header">Edit Roles {{link_to_route('roles.index','List All',null,array('class'=>'btn btn-success pull-right'))}}</h1>   
{{ Form::model($roles,array('route' => array('roles.update', $roles->id),'class'=>'form-horizontal','method' => 'PUT')) }}  
<div class="row">		
	<div class="form-group">
		<label class="control-label col-sm-6">Role Name <sup>*</sup> :</label>
		<div class="col-md-6">
			{{Form::text('name',null,array('class' => 'form-control', 'required' =>'required'))}}
			{{ $errors->first('name', '<p class="text-danger">:message</p>' ) }}
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-6">Role Description : <sup>(maximum length should be 600)</sup></label>
		<div class="col-md-6">
			{{Form::textarea('description',null,array('class' => 'form-control', 'rows'=>'3'))}}
		</div>
	</div>
	@if(($roles->is_deletable)==1) 
	<div class="form-group">
		<div class="col-md-6">
			<label class="control-label col-sm-6">Status :</label>
			<div class="col-md-6">
				{{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}
			</div>
		</div>
	</div>
	@else
	{{Form::hidden('status',1)}}
	@endif

</div>

<h3> Permission :</h3>
@foreach ($controllersArray as $elements)
<div class="row">						
	@foreach ($elements as $parent)	
	<div class="col-md-4">			
		<div class="checkbox controller">
			<label><input name="permissions[]" class="parent_{{ $parent['name'] }}" type="checkbox" <?php echo in_array($parent['id'], $checkPermissions)? "checked='true'":"" ;?> value="{{ $parent['id']}}" onChange="permission_select_deselect_child(this)"> <strong>{{ $parent['name'] }} </strong></label>
		</div>
		<div class="action-wraper">
			@foreach ($parent['children'] as $child) 
			<div class="checkbox actions" style="margin-left:20px;">
				<label><input name="permissions[]" class="{{ $parent['name'] }}" type="checkbox" <?php echo in_array($child['id'], $checkPermissions)? "checked='true'":"" ;?> value="{{ $child['id'] }}" onChange="permission_select_parent('{{ $parent['name'] }}')"> {{ $child['name'] }}</label>
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

