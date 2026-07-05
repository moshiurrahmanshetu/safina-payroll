@extends('layouts.admin')
@section('title', 'Update Suppplier Info')
@section('content')
<h3 class="page-header">Update Suppplier Info {{link_to_route('supplier.index',' Supplier List',null,array('class'=>'btn btn-success pull-right'))}} </h3>
{{ Form::model($suppliers,array('route' => array('supplier.update', $suppliers->id), 'class'=>'form-horizontal', 'method' => 'PUT')) }} 
	<div class="row">
		<div class="col-md-12 multi-column">              
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Contact Name <sup>*</sup></label>
          {{Form::text('contact_name',null, array('class' => 'form-control', 'required'=>'required'))}}
          {!! $errors->first('contact_name', '<p class="text-danger">:message</p>' ) !!}     
        </div>
      </div> 
     <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Company Name <sup>*</sup></label>
          {{Form::text('company_name',null, array('class' => 'form-control', 'required'=>'required'))}}
          {!! $errors->first('company_name', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>          
    </div>
    <div class="col-md-12 multi-column">              
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Mobile No<sup>*</sup></label>
          {{Form::text('mobile',null, array('class' => 'form-control','required'=>'required'))}}
          {!! $errors->first('mobile', '<p class="text-danger">:message</p>' ) !!}     
        </div>
      </div>     
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Address </label>
          {{Form::text('address',null, array('class' => 'form-control'))}}
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">E-Mail </label>
          {{Form::text('email',null, array('class' => 'form-control'))}}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Website</label>
          {{Form::text('web_site',null, array('class' => 'form-control'))}}
        </div>
      </div>             
    </div>
    <div class="col-md-12 multi-column">              
       <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Supplier Type <sup>*</sup></label>
          {{Form::select('supplier_type',config('myhelpers.supplier_type'),null,array('class' => 'form-control', 'required'=>'required'))}}
          {!! $errors->first('contact_name', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>  
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Status</label>
          {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}     
        </div>
      </div>            
    </div>
  	<div class="form-group">
			<div class="col-md-6 col-md-offset-2">
				<button type="submit" class="btn btn-primary">
					Update Supplier
				</button>
			</div>
	 </div>
	</div>
 {{ Form::close() }}
@endsection