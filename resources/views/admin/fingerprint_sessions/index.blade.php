@extends('layouts.admin')
@section('title', 'Fingerprint Sessions')
@section('content')
<h3 class="page-header">Fingerprint Sessions</h3>

<div class="row">
  <div class="col-md-12">
    <div class="text-right mb-3">
      <form action="{{ route('fingerprint_sessions.process') }}" method="POST" style="display:inline">
        @csrf
        <button type="submit" class="btn btn-success" onclick="return confirm('Process all pending fingerprint logs?')">
          <i class="fa fa-cogs"></i> Process Pending Logs
        </button>
      </form>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h4>Total Sessions</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['total'] }}</h2>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-success">
              <div class="panel-heading">
                <h4>Processed</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['processed'] }}</h2>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-warning">
              <div class="panel-heading">
                <h4>Pending</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['pending'] }}</h2>
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
            <th>Employee</th>
            <th>Attendance Date</th>
            <th>Shift</th>
            <th>First IN</th>
            <th>Last OUT</th>
            <th>Total Punch</th>
            <th>Remarks</th>
            <th>Processed</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sessions as $session)
            <tr>
              <td>{{ $session->id }}</td>
              <td>{{ $session->user ? $session->user->name : 'N/A' }}</td>
              <td>{{ $session->attendance_date }}</td>
              <td>{{ $session->shift ? $session->shift->name : 'N/A' }}</td>
              <td>{{ $session->first_in ? $session->first_in->format('H:i:s') : '-' }}</td>
              <td>{{ $session->last_out ? $session->last_out->format('H:i:s') : '-' }}</td>
              <td>{{ $session->total_punch }}</td>
              <td>{{ $session->remarks ?? '-' }}</td>
              <td>{!! $session->processed_badge !!}</td>
              <td>
                <a href="{{ route('fingerprint_sessions.show', $session->id) }}" class="btn btn-sm btn-info">
                  <i class="fa fa-eye"></i>
                </a>
                <form action="{{ route('fingerprint_sessions.destroy', $session->id) }}" method="POST" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this session?')">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    {{ $sessions->links() }}
  </div>
</div>

@endsection
