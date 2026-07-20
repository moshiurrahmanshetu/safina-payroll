@extends('layouts.admin')
@section('title', 'View Fingerprint Log')
@section('content')
<h3 class="page-header">Fingerprint Log Details</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <table class="table table-bordered">
          <tr>
            <th style="width: 30%;">ID</th>
            <td>{{ $log->id }}</td>
          </tr>
          <tr>
            <th>Employee Code</th>
            <td>{{ $log->employee_code }}</td>
          </tr>
          <tr>
            <th>Punch Datetime</th>
            <td>{{ $log->punch_datetime }}</td>
          </tr>
          <tr>
            <th>Punch Type</th>
            <td>
              @if($log->punch_type == 'IN')
                <span class="badge badge-success">IN</span>
              @else
                <span class="badge badge-danger">OUT</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Device ID</th>
            <td>{{ $log->device_id ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Source</th>
            <td>{{ $log->source }}</td>
          </tr>
          <tr>
            <th>Import Batch</th>
            <td>{{ $log->import_batch }}</td>
          </tr>
          <tr>
            <th>Processed</th>
            <td>{!! $log->processed_badge !!}</td>
          </tr>
          <tr>
            <th>Processed At</th>
            <td>{{ $log->processed_at ?? 'Not processed yet' }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>{{ $log->status }}</td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ $log->created_at }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ $log->updated_at }}</td>
          </tr>
        </table>

        <div class="text-right">
          <a href="{{ route('fingerprint_logs.index') }}" class="btn btn-danger">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
