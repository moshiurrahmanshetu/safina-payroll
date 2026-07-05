@extends('layouts.admin')
@section('title', 'Create Category')
@section('content')
<h1 class="page-header">Create Category </h1>
{{ Form::model(Request::old(),array('route' => array('category.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Category Name <sup>*</sup></label>
        {{Form::text('name',null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-8">
      <div class="form-group">
        <label class="control-label">Remarks</label>
          {{Form::textarea('remarks',null,array('class' => 'form-control', 'rows'=>4))}}
          {!! $errors->first('remarks', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>

  </div>
	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				Create Category
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
            <th>Remarks</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@php $id= 1; $category = '';@endphp
					@foreach ($categories as $data)
					@php
					$category.='<option value="'.$data->id.'">'.$data->name.'</option>';
					@endphp
					<tr>
						<td>{{ $id}}</td>
						<td>{{ $data->name }}</td>
            <td> {{$data->remarks}} </td>
						<td>
							<button type="button" data-toggle="modal" data-target="#deleteModal" onClick="deleteCategory('{{$data->id}}')" class='btn btn-danger btn-xs delete-button'>×</button>
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

<!-- Delete Category modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Select any Category for all the Products under this Category</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				{{ Form::open(array('route' => array('category.destroy', 'remove-id'), 'method'=>'DELETE', 'id'=>'CategoryDelete')) }}  
				{{Form::select('category_id',array(),null,array('class' => 'form-control', 'id'=>'selectDesBox'))}} 
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
	function deleteCategory(selector){
		var category='<?php if(isset($category)){ echo $category; } ?>'; 
		var my_obj = $('#CategoryDelete');
		var my_action = my_obj.attr('action');
		var my_id = selector;
		var urlaction = my_action.substring(0, my_action.lastIndexOf("/") + 1);
		var my_actions = urlaction+my_id;
		my_obj.attr('action', my_actions);
		$('#selectDesBox').empty();
		$("#selectDesBox").append(category);
		$("#selectDesBox option[value='"+my_id+"']").remove();
	}
</script>
@endsection