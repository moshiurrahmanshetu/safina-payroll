@extends('layouts.admin')
@section('title', 'Customer Information Fields')
@section('content')
<h3 class="page-header">Customer Information Fields for: {{$category->name}} {{link_to_route('service_categories.index','Category List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model(Request::old(),array('route' => array('category_meta_fields.store',$category->id),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
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
        <label class="control-label">&nbsp;</label>
        <button type="submit" class="btn btn-primary form-control">Add Field</button>
      </div>
    </div>
  </div>
  <div class="col-md-12 multi-column" id="options_div" style="display:none;">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Options</label>
        <div id="options_container">
          <div class="input-group" style="margin-bottom: 10px;">
            <input type="text" name="options_array[]" class="form-control" placeholder="Option value">
            <span class="input-group-btn">
              <button type="button" class="btn btn-danger" onclick="removeOption(this)">Remove</button>
            </span>
          </div>
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
</div>
{{ Form::close() }}

<div class="row">
  <div class="col-md-12">
    <div class="alert alert-info">
      <i class="nav-icon icon-info"></i> Drag and drop rows to reorder fields. Click <i class="nav-icon icon-pencil"></i> to edit field.
    </div>
    <table class="table table-striped" id="meta_fields_table">
      <thead>
        <tr>
          <th style="width: 40px;"></th>
          <th style="width: 50px;">#</th>
          <th>Field Name</th>
          <th>Field Type</th>
          <th>Required</th>
          <th>Options</th>
          <th>Conditional</th>
          <th style="width: 80px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($meta_fields as $index => $field)
        <tr data-id="{{ $field->id }}">
          <td><i class="nav-icon icon-arrows-alt"></i></td>
          <td>{{ $index + 1 }}</td>
          <td>{{ $field->field_name }}</td>
          <td>{{ config('myhelpers.field_type')[$field->field_type] ?? '' }}</td>
          <td>{{ $field->required ? 'Yes' : 'No' }}</td>
          <td>
            @if($field->options)
              @php
                $fieldOptions = json_decode($field->options, true);
                if(is_array($fieldOptions)){
                  echo implode(', ', $fieldOptions);
                } else {
                  echo $field->options;
                }
              @endphp
            @else
              -
            @endif
          </td>
          <td>
            @if($field->conditional_field)
              {{ $field->conditional_field }} = {{ $field->conditional_value }}
            @else
              -
            @endif
          </td>
          <td>
            <a href="{{ route('category_meta_fields.edit', [$category->id, $field->id]) }}" class="btn btn-sm btn-primary"><i class="nav-icon icon-pencil"></i></a>
            <form action="{{ route('category_meta_fields.destroy', [$category->id, $field->id]) }}" method="POST" style="display:inline;">
              {{ csrf_field() }}
              {{ method_field('DELETE') }}
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="nav-icon icon-trash"></i></button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<script>
function showOptions(fieldType) {
  var optionsDiv = document.getElementById('options_div');
  if (fieldType == 2) { // Select type
    optionsDiv.style.display = 'block';
  } else {
    optionsDiv.style.display = 'none';
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

// Initialize sortable table
$(document).ready(function() {
  $('#meta_fields_table tbody').sortable({
    handle: 'td:first-child',
    stop: function(event, ui) {
      var orders = {};
      $('#meta_fields_table tbody tr').each(function(index) {
        orders[$(this).data('id')] = index;
      });
      $.ajax({
        url: '{{ route('category_meta_fields.update_sort_order') }}',
        type: 'POST',
        data: { orders: orders, _token: '{{ csrf_token() }}' },
        success: function(response) {
          console.log('Sort order updated');
        }
      });
    }
  });
});
</script>
@endsection
