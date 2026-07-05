@extends('layouts.admin')
@section('title', 'Create Designation')
@section('content')
<h1 class="page-header">Create Designation </h1>
{{ Form::model(Request::old(),array('route' => array('designation.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">

	<div class="form-group">
		<label class="control-label col-sm-2">Designation Name <sup>*</sup> :</label>
		<div class="col-md-6">
			{{Form::text('name',null,array('class' => 'form-control', 'required'=>'required'))}}
			{!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				Create Designation
			</button>
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-8 col-md-offset-1">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>SL NO</th>
						<th>Name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@php $id= 1; $designation = '';@endphp
					@foreach ($designations as $data)
					@php $name=addslashes($data->name);
					$designation.='<option value="'.$data->id.'">'.$name.'</option>';
					@endphp
					<tr>
						<td>{{ $id}}</td>
						<td>{{ $data->name }}</td>
						<td>
							<button type="button" data-toggle="modal" data-target="#deleteModal" onClick="deleteDesignation('{{$data->id}}')" class='btn btn-danger btn-xs delete-button'>×</button>
						</td>
					</tr>
					@php $id++; @endphp
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
{{ Form::close() }}

<!-- Delete designation modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Select any Designation for all the users under this Designation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				{{ Form::open(array('route' => array('designation.destroy', 'remove-id'),'method'=>'DELETE',
				'class' =>'delete-form2', 'id'=>'designationDelete')) }}  
				{{Form::select('designation_id',array(),null,array('class' => 'form-control', 'id'=>'selectDesBox'))}} 
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				{{ Form::submit('Confirm Delete',array('class'=>'btn btn-primary delete-form'))}}
			</div>
			{{ Form::close() }}
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@section('script')
<script> 
	function deleteDesignation(selector){
		var designation='<?php if(isset($designation)){ echo $designation; } ?>'; 
		var my_obj = $('#designationDelete');
		var my_action = my_obj.attr('action');
		var my_id = selector;

		var urlaction = my_action.substring(0, my_action.lastIndexOf("/") + 1);
		var my_actions = urlaction+my_id;

		my_obj.attr('action', my_actions);
		$('#selectDesBox').empty();
		$("#selectDesBox").append(designation);
		$("#selectDesBox option[value='"+my_id+"']").remove();
	}
</script>
@endsection


