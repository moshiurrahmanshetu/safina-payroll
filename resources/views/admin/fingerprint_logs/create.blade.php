@extends('layouts.admin')
@section('title', 'Fingerprint CSV Import')
@section('content')
<h3 class="page-header">Fingerprint CSV Import</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        {{ Form::open(array('route' => 'fingerprint_logs.store', 'method'=>'POST', 'files'=>true, 'class'=>'form-horizontal')) }}
        
        <div class="form-group">
          <label class="col-md-3 control-label">CSV File</label>
          <div class="col-md-6">
            {{ Form::file('csv_file', ['class' => 'form-control', 'required']) }}
            <small class="text-muted">Only CSV and TXT files allowed</small>
            @if($errors->has('csv_file'))
              <span class="text-danger">{{ $errors->first('csv_file') }}</span>
            @endif
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-6 col-md-offset-3">
            {{ Form::submit('Import CSV', ['class' => 'btn btn-success']) }}
            <a href="{{ route('fingerprint_logs.index') }}" class="btn btn-danger">Cancel</a>
          </div>
        </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>CSV Format</h4>
      </div>
      <div class="panel-body">
        <p><strong>Supported Format:</strong></p>
        <pre>Employee Code,Punch Date,Punch Time,Punch Type</pre>
        
        <p><strong>Example:</strong></p>
        <pre>EMP001,2026-07-15,08:01:20,IN
EMP001,2026-07-15,16:03:11,OUT
EMP002,2026-07-15,08:15:10,IN
EMP002,2026-07-15,17:05:21,OUT</pre>
        
        <p><strong>Validation Rules:</strong></p>
        <ul>
          <li>Employee Code cannot be empty</li>
          <li>Punch Type must be IN or OUT</li>
          <li>Date and Time must be valid</li>
          <li>Invalid rows will be skipped</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@endsection
