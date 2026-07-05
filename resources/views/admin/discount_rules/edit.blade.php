@extends('layouts.admin')
@section('title', 'Promo Discount Edit')
@section('content')
<h3 class="page-header">Promo Discount Edit {{link_to_route('discount_rules.index','Promo Discount List',[],array('class'=>'btn btn-success pull-right'))}}</h3>
{{ Form::model($discount_rule,array('route' => array('discount_rules.update',$discount_rule->id),'method'=>'PUT','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Name *</label>
        {{Form::text('name',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('name', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Promo Code</label>
        {{Form::text('code',null, array('class' => 'form-control','placeholder'=>'Enter unique promo code'))}}
        {!! $errors->first('code', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Category (Optional)</label>
        <select name="category_id" id="category_id" class="form-control" onchange="loadServicesByCategory(this.value)">
          <option value="">All Categories</option>
          @foreach($service_categories as $id => $name)
            <option value="{{ $id }}" {{ $discount_rule->category_id == $id ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
        {!! $errors->first('category_id', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Service (Optional - leave blank for all)</label>
        <select name="service_id" id="service_id" class="form-control">
          <option value="">All Services</option>
          @foreach($services as $id => $name)
            <option value="{{ $id }}" {{ $discount_rule->service_id == $id ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
        {!! $errors->first('service_id', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Discount Type *</label>
        {{Form::select('discount_type',config('myhelpers.discount_type'),null,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('discount_type', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Amount *</label>
        {{Form::number('amount',null, array('class' => 'form-control','required'=>'required','step'=>'0.01'))}}
        {!! $errors->first('amount', '<p class="text-danger">:message</p>' ) !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Start Date (Optional)</label>
        {{Form::date('start_date',null, array('class' => 'form-control'))}}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">End Date (Optional)</label>
        {{Form::date('end_date',null, array('class' => 'form-control'))}}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
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
          Update Promo Discount
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection

@section('script')
<script>
// Load services by category via AJAX
function loadServicesByCategory(category_id){
  var serviceSelect = document.getElementById('service_id');

  if(!category_id){
    // Show all services when no category selected
    serviceSelect.innerHTML = '<option value="">All Services</option>';
    @foreach($services as $id => $name)
      serviceSelect.innerHTML += '<option value="{{ $id }}">{{ $name }}</option>';
    @endforeach
    return;
  }

  // Show loading
  serviceSelect.innerHTML = '<option value="">Loading...</option>';

  // Fetch services via AJAX
  $.ajax({
    url: "{{ url('/admin/get-services') }}/" + category_id,
    type: 'GET',
    dataType: 'json',
    success: function(response){
      if(response.status === true){
        var options = '<option value="">All Services</option>';

        if(response.data.length === 0){
          options = '<option value="">No services available for this category</option>';
        } else {
          for(var i = 0; i < response.data.length; i++){
            var service = response.data[i];
            options += '<option value="' + service.id + '">' + service.name + '</option>';
          }
        }

        serviceSelect.innerHTML = options;
      } else {
        console.error('Error in response:', response.message);
        serviceSelect.innerHTML = '<option value="">' + (response.message || 'Error loading services') + '</option>';
      }
    },
    error: function(xhr, status, error){
      console.error('Error loading services:', error);
      serviceSelect.innerHTML = '<option value="">Error loading services</option>';
    }
  });
}
</script>
@endsection
