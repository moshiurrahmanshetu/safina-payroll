@extends('layouts.admin')
@section('title', 'Fingerprint Logs')
@section('content')
<h3 class="page-header">Fingerprint Logs</h3>

<div class="row">
  <div class="col-md-12">
    <div class="text-right mb-3">
      <a href="{{ route('fingerprint_logs.create') }}" class="btn btn-primary">
        <i class="fa fa-upload"></i> Import CSV
      </a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-3">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h4>Total Logs</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['total'] }}</h2>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-success">
              <div class="panel-heading">
                <h4>Processed</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['processed'] }}</h2>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-warning">
              <div class="panel-heading">
                <h4>Pending</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['pending'] }}</h2>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h4>Recent Batches</h4>
              </div>
              <div class="panel-body">
                <ul class="list-unstyled">
                  @foreach($recentBatches as $batch)
                    <li>{{ $batch }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Employee Code</th>
            <th>Punch Datetime</th>
            <th>Punch Type</th>
            <th>Import Batch</th>
            <th>Source</th>
            <th>Processed</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
            <tr>
              <td>{{ $log->id }}</td>
              <td>{{ $log->employee_code }}</td>
              <td>{{ $log->punch_datetime }}</td>
              <td>
                @if($log->punch_type == 'IN')
                  <span class="badge badge-success">IN</span>
                @else
                  <span class="badge badge-danger">OUT</span>
                @endif
              </td>
              <td>{{ $log->import_batch }}</td>
              <td>{{ $log->source }}</td>
              <td>{!! $log->processed_badge !!}</td>
              <td>{{ $log->created_at }}</td>
              <td>
                <a href="{{ route('fingerprint_logs.show', $log->id) }}" class="btn btn-sm btn-info">
                  <i class="fa fa-eye"></i>
                </a>
                <form action="{{ route('fingerprint_logs.destroy', $log->id) }}" method="POST" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this log?')">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    {{ $logs->links() }}
  </div>
</div>

@endsection
