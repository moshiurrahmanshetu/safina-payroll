@extends('layouts.admin')
@section('title', 'Package Cash Handover Approval')
@section('content')
<h3 class="page-header">Package Cash Handover Approval</h3>

<!-- Filters -->
<div class="panel-body">
    {{ Form::open(array('route' => 'package_cash_handovers.approval', 'method'=>'GET')) }}

    <div class="row">

        <!-- From Date -->
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">From Date</label>
                <input type="date" name="from_date" class="form-control"
                       value="{{ request('from_date') }}">
            </div>
        </div>

        <!-- To Date -->
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">To Date</label>
                <input type="date" name="to_date" class="form-control"
                       value="{{ request('to_date') }}">
            </div>
        </div>

        <!-- User -->
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">User</label>
                <select name="user_id" class="form-control">
                    <option value="">All Users</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Counter -->
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Counter</label>
                <select name="counter_id" class="form-control">
                    <option value="">All Counters</option>
                    @foreach($counters as $id => $name)
                        <option value="{{ $id }}" {{ request('counter_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Status -->
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="col-md-2">
            <div class="form-group" style="margin-top:25px;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('package_cash_handovers.approval') }}" class="btn btn-danger">Reset</a>
            </div>
        </div>

    </div>

    {{ Form::close() }}
</div>
<br>

<!-- Handovers Table -->
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>User</th>
              <th>Counter</th>
              <th>Amount</th>
              <th>Business Date</th>
              <th>Requested At</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($handovers as $handover)
            <tr>
              <td>{{ $handover->id }}</td>
              <td>{{ $handover->user->name ?? 'N/A' }}</td>
              <td>{{ $handover->counter->name ?? 'N/A' }}</td>
              <td>{{ number_format($handover->amount, 2) }}</td>
              <td>{{ $handover->business_date->format('d-m-Y') }}</td>
              <td>{{ $handover->requested_at->format('d-m-Y (H:i)') }}</td>
              <td>
                @if($handover->status == 'pending')
                  <span class="badge badge-warning">Pending</span>
                @elseif($handover->status == 'approved')
                  <span class="badge badge-success">Approved</span>
                @elseif($handover->status == 'rejected')
                  <span class="badge badge-danger">Rejected</span>
                @endif
              </td>
              <td>
                @if($handover->status == 'pending')
                  {{ Form::open(array('route' => ['package_cash_handovers.approve', $handover->id], 'method'=>'POST', 'class'=>'form-inline')) }}
                  <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this handover?')">Approve</button>
                  {{ Form::close() }}
                  
                  <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $handover->id }}">Reject</button>
                  
                  <!-- Reject Modal -->
                  <div class="modal fade" id="rejectModal{{ $handover->id }}" tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title">Reject Handover</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                          {{ Form::open(array('route' => ['package_cash_handovers.reject', $handover->id], 'method'=>'POST')) }}
                          <div class="form-group">
                            <label>Remark (Optional):</label>
                            <textarea name="remark" class="form-control" rows="3"></textarea>
                          </div>
                          <button type="submit" class="btn btn-danger">Reject</button>
                          {{ Form::close() }}
                        </div>
                      </div>
                    </div>
                  </div>
                @else
                  <span class="text-muted">No actions</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
