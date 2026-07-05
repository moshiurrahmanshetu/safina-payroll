@extends('layouts.admin')
@section('title', 'Edit Customer Information Field')
@section('content')
<h3 class="page-header">Edit Customer Information Field for: {{$category->name}} {{link_to_route('category_meta_fields.create','Back to Fields',[$category->id],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model($meta_field, array('route' => array('category_meta_fields.update', $category->id, $meta_field->id), 'method'=>'PUT', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Field Name *</label>
        {{Form::text('field_name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('field_name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label class="control-label">Field Type *</label>
        {{Form::select('field_type',config('myhelpers.field_type'),null,array('class' => 'form-control', 'required'=>'required','onchange'=>'showOptions(this.value)'))}}
        {!! $errors->first('field_type', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label class="control-label">Required</label>
        {{Form::select('required',['0'=>'No','1'=>'Yes'],null,array('class' => 'form-control'))}}
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label class="control-label">Sort Order</label>
        {{Form::number('sort_order',null,array('class' => 'form-control', 'min'=>0))}}
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column" id="options_div" style="{{ $meta_field->field_type == 2 ? 'display:block;' : 'display:none;' }}">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Options</label>
        <div id="options_container">
          @php
            $fieldOptions = [];
            if($meta_field->options){
              $decoded = json_decode($meta_field->options, true);
              if(is_array($decoded)){
                $fieldOptions = $decoded;
              } else {
                // Handle old JSON format with keys
                $decoded = json_decode($meta_field->options, true);
                if(is_array($decoded)){
                  $fieldOptions = array_values($decoded);
                }
              }
            }
          @endphp
          @if(count($fieldOptions) > 0)
            @foreach($fieldOptions as $option)
            <div class="input-group" style="margin-bottom: 10px;">
              <input type="text" name="options_array[]" class="form-control" placeholder="Option value" value="{{ $option }}">
              <span class="input-group-btn">
                <button type="button" class="btn btn-danger" onclick="removeOption(this)">Remove</button>
              </span>
            </div>
            @endforeach
          @else
            <div class="input-group" style="margin-bottom: 10px;">
              <input type="text" name="options_array[]" class="form-control" placeholder="Option value">
              <span class="input-group-btn">
                <button type="button" class="btn btn-danger" onclick="removeOption(this)">Remove</button>
              </span>
            </div>
          @endif
        </div>
        <button type="button" class="btn btn-sm btn-success" onclick="addOption()">+ Add Option</button>
        <small class="text-muted">Required for Select type. Add option values one by one.</small>
      </div>
    </div>
  </div>

  <!-- Conditional Logic Section -->
  <div class="col-md-12 multi-column" style="background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px;">
    <div class="col-md-12">
      <h5><i class="nav-icon icon-link"></i> Conditional Logic (Optional)</h5>
      <p class="text-muted">Show this field only when another field matches a specific value</p>
    </div>
    <div class="col-md-5">
      <div class="form-group">
        <label class="control-label">Conditional Field (Select type only)</label>
        {{Form::select('conditional_field',array(''=>'-- None --')+$conditional_fields,null,array('class' => 'form-control'))}}
        <small class="text-muted">Select a dropdown field that triggers this</small>
      </div>
    </div>
    <div class="col-md-5">
      <div class="form-group">
        <label class="control-label">Conditional Value</label>
        {{Form::text('conditional_value',null,array('class' => 'form-control', 'placeholder'=>'e.g. married, yes, etc.'))}}
        <small class="text-muted">Enter the option key that shows this field</small>
      </div>
    </div>
  </div>

  <!-- Help Text / Instruction -->
  <div class="col-md-12 multi-column" style="background: #e2e3e5; padding: 15px; margin: 10px 0; border-radius: 5px;">
    <div class="col-md-12">
      <h5><i class="nav-icon icon-question"></i> Help Text / Instruction (Optional)</h5>
      <p class="text-muted">Display additional instructions below the field in booking forms</p>
    </div>
    <div class="col-md-8">
      <div class="form-group">
        <label class="control-label">Help Text</label>
        {{Form::textarea('help_text',null,array('class' => 'form-control', 'rows'=>3, 'placeholder'=>'Example:\nMax image size 1 MB.\nImage ratio 300x300.\nPDF only.'))}}
        <small class="text-muted">Optional. This text will be displayed below the field in booking create/edit forms.</small>
      </div>
    </div>
  </div>

  <div class="col-md-12">
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Update Field</button>
      <a href="{{ route('category_meta_fields.create', $category->id) }}" class="btn btn-danger">Cancel</a>
    </div>
  </div>
</div>
{{ Form::close() }}

@endsection

@section('script')
<script>
function showOptions(field_type){
  if(field_type == 2){
    document.getElementById('options_div').style.display = 'block';
  }else{
    document.getElementById('options_div').style.display = 'none';
  }
}

function addOption() {
  var container = document.getElementById('options_container');
  var div = document.createElement('div');
  div.className = 'input-group';
  div.style.marginBottom = '10px';
  div.innerHTML = '<input type="text" name="options_array[]" class="form-control" placeholder="Option value">' +
    '<span class="input-group-btn">' +
    '<button type="button" class="btn btn-danger" onclick="removeOption(this)">Remove</button>' +
    '</span>';
  container.appendChild(div);
}

function removeOption(button) {
  var container = document.getElementById('options_container');
  if (container.children.length > 1) {
    button.closest('.input-group').remove();
  }
}
</script>
@endsection
