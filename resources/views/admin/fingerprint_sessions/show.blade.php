@extends('layouts.admin')
@section('title', 'View Fingerprint Session')
@section('content')
<h3 class="page-header">Fingerprint Session Details</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <table class="table table-bordered">
          <tr>
            <th style="width: 30%;">ID</th>
            <td>{{ $session->id }}</td>
          </tr>
          <tr>
            <th>Employee</th>
            <td>{{ $session->user ? $session->user->name : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Attendance Date</th>
            <td>{{ $session->attendance_date }}</td>
          </tr>
          <tr>
            <th>Shift</th>
            <td>{{ $session->shift ? $session->shift->name : 'N/A' }}</td>
          </tr>
          <tr>
            <th>First IN</th>
            <td>{{ $session->first_in ? $session->first_in->format('Y-m-d H:i:s') : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Last OUT</th>
            <td>{{ $session->last_out ? $session->last_out->format('Y-m-d H:i:s') : 'N/A' }}</td>
          </tr>
          <tr>
            <th>Total Punch</th>
            <td>{{ $session->total_punch }}</td>
          </tr>
          <tr>
            <th>Source</th>
            <td>{{ $session->source }}</td>
          </tr>
          <tr>
            <th>Processed</th>
            <td>{!! $session->processed_badge !!}</td>
          </tr>
          <tr>
            <th>Processed At</th>
            <td>{{ $session->processed_at ?? 'Not processed yet' }}</td>
          </tr>
          <tr>
            <th>Remarks</th>
            <td>{{ $session->remarks ?? '-' }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>{{ $session->status }}</td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ $session->created_at }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ $session->updated_at }}</td>
          </tr>
        </table>

        <div class="text-right">
          <a href="{{ route('fingerprint_sessions.index') }}" class="btn btn-danger">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
