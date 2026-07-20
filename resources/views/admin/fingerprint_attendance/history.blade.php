@extends('layouts.admin')
@section('title', 'Attendance Generation History')
@section('content')
<h3 class="page-header">Attendance Generation History</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <form action="{{ route('fingerprint_attendance.history') }}" method="GET" class="form-inline">
          <div class="form-group mr-2">
            <label>Employee:</label>
            <select name="user_id" class="form-control">
              <option value="">All Employees</option>
              @foreach(\App\Models\User::all() as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group mr-2">
            <label>Start Date:</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
          </div>
          <div class="form-group mr-2">
            <label>End Date:</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
          </div>
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="{{ route('fingerprint_attendance.index') }}" class="btn btn-danger">Back</a>
        </form>
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
            <th>Status</th>
            <th>Processed At</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sessions as $session)
            <tr>
              <td>{{ $session->id }}</td>
              <td>{{ $session->user ? $session->user->name : 'N/A' }}</td>
              <td>{{ $session->attendance_date }}</td>
              <td>{{ $session->shift ? $session->shift->name : 'N/A' }}</td>
              <td>
                @if($session->status == 'Completed')
                  <span class="badge badge-success">{{ $session->status }}</span>
                @elseif($session->status == 'Skipped')
                  <span class="badge badge-warning">{{ $session->status }}</span>
                @else
                  <span class="badge badge-info">{{ $session->status }}</span>
                @endif
              </td>
              <td>{{ $session->processed_at ? $session->processed_at->format('Y-m-d H:i:s') : '-' }}</td>
              <td>{{ $session->remarks ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    {{ $sessions->links() }}
  </div>
</div>

@endsection
