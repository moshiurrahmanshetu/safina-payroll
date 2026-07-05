@extends('layouts.admin')
@section('title', 'Create Ticket Category')
@section('content')
<h1 class="page-header">Create Ticket Category </h1>
{{ Form::model(Request::old(),array('route' => array('ticket_category.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Category Name *</label>
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
{{ Form::close() }}

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
					@php $id= 1; @endphp
					@foreach ($ticket_categories as $data)
					<tr>
						<td>{{ $id}}</td>
						<td>{{ $data->name }}</td>
            <td> {{$data->remarks}} </td>
						<td>
							{{ Form::open(array('route' => array('ticket_category.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form-'.$data->id)) }}
							<button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
							{{ Form::close() }}
						</td>
					</tr>
					@php $id++; @endphp
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection
