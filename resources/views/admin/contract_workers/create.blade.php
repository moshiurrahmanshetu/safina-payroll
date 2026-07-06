@extends('layouts.admin')
@section('title', 'Contract Worker Create')
@section('content')
<h3 class="page-header">Contract Worker Create {{link_to_route('contract_workers.index','Contract Worker List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model(Request::old(),array('route' => array('contract_workers.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Select Worker Type</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Worker Type *</label>
          <div>
            <label class="radio-inline">
              <input type="radio" name="worker_type" value="existing" onchange="toggleWorkerType()" checked> Select Existing User
            </label>
            <label class="radio-inline">
              <input type="radio" name="worker_type" value="new" onchange="toggleWorkerType()"> New Contract Worker
            </label>
          </div>
        </div>

        <!-- Existing User Section -->
        <div id="existing_user_section">
          <div class="form-group">
            <label class="control-label">Select User *</label>
            <select name="user_id" id="user_id" class="form-control" onchange="loadUserDetails()">
              <option value="">Select User</option>
              @foreach($users as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
            {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
          </div>
        </div>

        <!-- New Worker Section -->
        <div id="new_worker_section" style="display: none;">
          <div class="alert alert-info">
            <strong>Note:</strong> Fill in all worker details manually.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Worker Photo</label>
        {{Form::file('photo', array('class' => 'form-control'))}}
        {!! $errors->first('photo', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Full Name *</label>
        {{Form::text('full_name',null, array('class' => 'form-control', 'required'=>'required', 'id'=>'full_name'))}}
        {!! $errors->first('full_name', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Mobile *</label>
        {{Form::text('mobile',null, array('class' => 'form-control', 'required'=>'required', 'id'=>'mobile'))}}
        {!! $errors->first('mobile', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Email</label>
        {{Form::email('email',null, array('class' => 'form-control', 'id'=>'email'))}}
        {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">NID</label>
        {{Form::text('nid',null, array('class' => 'form-control'))}}
        {!! $errors->first('nid', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Emergency Contact</label>
        {{Form::text('emergency_contact',null, array('class' => 'form-control'))}}
        {!! $errors->first('emergency_contact', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Present Address</label>
        {{Form::textarea('present_address',null, array('class' => 'form-control', 'rows'=>'3'))}}
        {!! $errors->first('present_address', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Work Area *</label>
        {{Form::select('work_area_id', [''=>'Select Work Area'] + $work_areas, null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('work_area_id', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Contract Title *</label>
        {{Form::text('contract_title',null, array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('contract_title', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Contract Amount *</label>
        {{Form::number('contract_amount',null, array('class' => 'form-control', 'required'=>'required', 'step'=>'0.01'))}}
        {!! $errors->first('contract_amount', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Advance Amount</label>
        {{Form::number('advance_amount',0, array('class' => 'form-control', 'step'=>'0.01'))}}
        {!! $errors->first('advance_amount', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Contract Start Date *</label>
        {{Form::date('contract_start_date',null, array('class' => 'form-control datetimepicker1', 'required'=>'required'))}}
        {!! $errors->first('contract_start_date', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Contract End Date *</label>
        {{Form::date('contract_end_date',null, array('class' => 'form-control datetimepicker1', 'required'=>'required'))}}
        {!! $errors->first('contract_end_date', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">Status *</label>
        {{Form::select('status',config('myhelpers.status'),1,array('class' => 'form-control', 'required'=>'required'))}}
        {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Notes</label>
        {{Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>'3'))}}
        {!! $errors->first('notes', '<p class="text-danger">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="col-md-12 multi-column">
    <div class="col-md-6">
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          Create Contract Worker
        </button>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection

@section('script')
<script>
function toggleWorkerType() {
  var workerType = document.querySelector('input[name="worker_type"]:checked').value;
  
  if (workerType === 'existing') {
    document.getElementById('existing_user_section').style.display = 'block';
    document.getElementById('new_worker_section').style.display = 'none';
    document.getElementById('user_id').required = true;
  } else {
    document.getElementById('existing_user_section').style.display = 'none';
    document.getElementById('new_worker_section').style.display = 'block';
    document.getElementById('user_id').required = false;
    document.getElementById('user_id').value = '';
  }
}

function loadUserDetails() {
  var userId = document.getElementById('user_id').value;
  
  if (!userId) {
    document.getElementById('full_name').value = '';
    document.getElementById('mobile').value = '';
    document.getElementById('email').value = '';
    return;
  }

  var url = '{{ route("contract_workers.get_user_details", ":id") }}'.replace(':id', userId);

  $.ajax({
    url: url,
    type: 'GET',
    success: function(response){
      if(response.success){
        document.getElementById('full_name').value = response.name;
        document.getElementById('mobile').value = response.mobile;
        document.getElementById('email').value = response.email;
      }
    },
    error: function(){
      alert('Failed to load user details');
    }
  });
}
</script>
@endsection
