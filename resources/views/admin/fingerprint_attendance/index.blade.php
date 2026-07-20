@extends('layouts.admin')
@section('title', 'Generate Attendance from Fingerprint')
@section('content')
<h3 class="page-header">Generate Attendance from Fingerprint</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h4>Pending Sessions</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['pending'] }}</h2>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-success">
              <div class="panel-heading">
                <h4>Completed Sessions</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['completed'] }}</h2>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h4>Total Sessions</h4>
              </div>
              <div class="panel-body text-center">
                <h2>{{ $stats['total'] }}</h2>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Generate Attendance</h4>
      </div>
      <div class="panel-body">
        <form action="{{ route('fingerprint_attendance.generate') }}" method="POST">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Generate All Pending</label>
                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Generate attendance for all pending sessions?')">
                  <i class="fa fa-cogs"></i> Generate All Pending
                </button>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Generate for Specific Date</label>
                <div class="input-group">
                  <input type="date" name="date" class="form-control" placeholder="Select Date">
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary">
                      <i class="fa fa-calendar"></i> Generate
                    </button>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Generate for Employee</label>
                <div class="input-group">
                  <select name="user_id" class="form-control">
                    <option value="">Select Employee</option>
                    @foreach(\App\Models\User::all() as $user)
                      <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                  </select>
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary">
                      <i class="fa fa-user"></i> Generate
                    </button>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>View History</label>
                <a href="{{ route('fingerprint_attendance.history') }}" class="btn btn-info btn-block">
                  <i class="fa fa-history"></i> View Generation History
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
