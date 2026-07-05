@extends('layouts.admin')
@section('title', 'Supplier Create')
@section('content')
<h3 class="page-header">Supplier Create {{link_to_route('supplier.index','Supplier List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
 {{ Form::model(Request::old(),array('route' => array('supplier.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
	<div class="row">
		<div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">
          <label for="name" class="control-label">Contact Name <sup>*</sup></label>
          {{Form::text('contact_name',null, array('class' => 'form-control', 'required'=>'required'))}}
            {!! $errors->first('contact_name', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div> 
     <div class="col-md-6">
        <div class="form-group">
          <label for="name" class="control-label">Company Name <sup>*</sup></label>
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
          <label for="accountno" class="control-label">Address </label>
           {{Form::text('address',null, array('class' => 'form-control'))}}
            {!! $errors->first('address', '<p class="text-danger">:message</p>' ) !!}
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
          <label for="accountno" class="control-label">Website</label>
           {{Form::text('web_site',null, array('class' => 'form-control'))}}
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
       <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Supplier Type <sup>*</sup></label>
          {{Form::select('supplier_type',config('myhelpers.supplier_type'),null,array('class' => 'form-control', 'required'=>'required'))}}
          {!! $errors->first('supplier_type', '<p class="text-danger">:message</p>' ) !!}
        </div>
      </div>  
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Status</label>
          {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))}}
        </div>
      </div>
    </div>
    <div class="col-md-12 multi-column">
      <div class="col-md-6">
        <div class="form-group">
          <button type="submit" class="btn btn-primary">
            Create Supplier
          </button>
        </div>
      </div>
    </div> 
    
</div>
{{ Form::close() }}
   
@endsection