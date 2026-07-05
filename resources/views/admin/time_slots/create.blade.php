@extends('layouts.admin')
@section('title', 'Time Slot Create')
@section('content')
<h3 class="page-header">Time Slot Create {{link_to_route('time-slots.index','Time Slot List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model(Request::old(),array('route' => array('time-slots.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Category</label>
        <select name="category_id" id="category_id" class="form-control">
          <option value="">Select Category (Optional)</option>
          @foreach($categories as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
          @endforeach
        </select>
        <small class="text-muted">Select a category to filter services</small>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Slot Name *</label>
        {{Form::text('name',null, array('class' => 'form-control','required'=>'required','placeholder'=>'e.g. Morning, Evening'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <div class="col-md-12">
    <div class="form-group">
      <label class="control-label">Services *</label>
      <div class="checkbox-container" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="select_all_services" onchange="toggleAllServices(this)">
          <label class="form-check-label" for="select_all_services" style="font-weight: bold;">
            Select All
          </label>
        </div>
        <hr>
        @foreach($services as $id => $name)
          <div class="form-check service-checkbox" data-category="{{ $serviceCategories[$id] ?? '' }}">
            <input class="form-check-input service-checkbox-input" type="checkbox" name="service_ids[]" value="{{ $id }}" id="service_{{ $id }}">
            <label class="form-check-label" for="service_{{ $id }}">
              {{ $name }}
            </label>
          </div>
        @endforeach
      </div>
      {!! $errors->first('service_ids', '<p class="text-danger">:message</p>' ) !!}
      <small class="text-muted">Select at least one service</small>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Start Time *</label>
        {{Form::time('start_time',null, array('class' => 'form-control','required'=>'required'))}}
        {!! $errors->first('start_time', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">End Time *</label>
        {{Form::time('end_time',null, array('class' => 'form-control','required'=>'required'))}}
        <small class="text-muted">Overnight slots allowed (e.g., 20:00 to 08:00)</small>
        {!! $errors->first('end_time', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Price (৳) *</label>
        {{Form::number('price',null, array('class' => 'form-control','required'=>'required','step'=>'0.01','min'=>'0'))}}
        {!! $errors->first('price', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Status *</label>
        {{Form::select('status',config('myhelpers.status'),1,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('status', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      {{Form::submit('Create',array('class'=>'btn btn-success'))}}
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection
@section('script')
<script>
  // Store service categories mapping
  const serviceCategories = @json($serviceCategories ?? []);

  function toggleAllServices(checkbox) {
    const checkboxes = document.querySelectorAll('.service-checkbox-input');
    checkboxes.forEach(cb => {
      cb.checked = checkbox.checked;
    });
  }

  // Filter services by category
  document.getElementById('category_id').addEventListener('change', function() {
    const selectedCategory = this.value;
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');

    serviceCheckboxes.forEach(checkbox => {
      const serviceCategory = checkbox.dataset.category;
      if (selectedCategory === '' || serviceCategory == selectedCategory) {
        checkbox.style.display = 'block';
      } else {
        checkbox.style.display = 'none';
        checkbox.querySelector('input').checked = false;
      }
    });
  });

  // Form validation - ensure at least one service is selected
  document.querySelector('form').addEventListener('submit', function(e) {
    const selectedServices = document.querySelectorAll('.service-checkbox-input:checked');
    if (selectedServices.length === 0) {
      e.preventDefault();
      alert('Please select at least one service');
      return false;
    }
  });
</script>
@endsection
