@extends('layouts.admin')
@section('title', 'Update Site Settings')
@section('content')
<h1 class="page-header">Update Site Settings</h1>   
{{ Form::model($site_settings,array('route' => array('site_settings.update',$site_settings->id),'enctype'=>'multipart/form-data','method' => 'PUT','class'=>'form-horizontal')) }}  
<div class="row">

	<div class="form-group">
		{{Form::label('Site Name:',null,array('class' => 'control-label col-sm-2'))}}
		<div class="col-md-6">	
			{{Form::text('name',null,array('class' => 'form-control'))}}
			{!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
		</div>
	</div>	

	<div class="form-group">
		{{Form::label('Email Address:',null,array('class' => 'control-label col-sm-2'))}}
		<div class="col-md-6">	
			{{Form::text('email',null,array('class' => 'form-control'))}}
			{!! $errors->first('email', '<p class="text-danger">:message</p>' ) !!}
		</div>
	</div>	

	<div class="form-group">
		{{Form::label('Company Logo:',null,array('class' => 'control-label col-sm-2'))}}
		<div class="col-md-6">
			{{Form::file('logo',array('onChange'=>'readURL(this)'))}}
			{!! $errors->first('logo', '<p class="text-danger">:message</p>' ) !!}
			<span>Width=160px, Height=42px</span>
			{{ Form::hidden('old_image',$site_settings->logo) }}
		</div>
		<div class="col-md-4 preview-div">
			{{ HTML::image('storage/app/admin/site_settings/'.$site_settings->logo,null,array('width'=>'100','class'=>'img-responsive')) }}	
		</div> 
	</div>

	<div class="form-group">
		{{Form::label('Alt Image:',null,array('class' => 'control-label col-sm-2'))}}
		<div class="col-md-6">	
			{{Form::text('logo_alt',null,array('class' => 'form-control'))}}
			{!! $errors->first('logo_alt', '<p class="text-danger">:message</p>' ) !!}
		</div>
	</div>	
	
	<div class="form-group">
		{{Form::label('PDF Header Image:',null,array('class' => 'control-label col-sm-2'))}}
		<div class="col-md-6">
			{{Form::file('pdf_header_img',array('onChange'=>'readURL(this)'))}}
			<span>Width=795px, Height=133px</span>
			{{ Form::hidden('old_pdf_header_img',$site_settings->pdf_header_img) }}
		</div>
		<div class="col-md-4 preview-div">
			{{ HTML::image('storage/app/admin/site_settings/'.$site_settings->pdf_header_img,null,array('width'=>'100','class'=>'img-responsive')) }}	
		</div> 
	</div>

	<div class="form-group">
		{{Form::label('PDF Footer Image:',null,array('class' => 'control-label col-sm-2'))}}
		<div class="col-md-6">
			{{Form::file('pdf_footer_img',array('onChange'=>'readURL(this)'))}}
			<span>Width=795px, Height=55px</span>
			{{ Form::hidden('old_pdf_footer_img',$site_settings->pdf_footer_img) }}
		</div>
		<div class="col-md-4 preview-div">
			{{ HTML::image('storage/app/admin/site_settings/'.$site_settings->pdf_footer_img,null,array('width'=>'100','class'=>'img-responsive')) }}	
		</div> 
	</div>

	<div class="form-group">
		{{Form::label('No Header Footer in PDF:',null,array('class' => 'control-label col-sm-2'))}}
		<div class="col-md-6">	
			{{Form::checkbox('pdf_no_header_footer',null,null,array('class' => 'form-control'))}}
		</div>
	</div>	


	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				Update Settings
			</button>
		</div>
	</div>
</div>
{{ Form::close() }}
@endsection 