@extends('layouts.admin')
@section('title', 'Shift Details')
@section('content')
<h3 class="page-header">Shift Details {{link_to_route('shifts.index','Shift List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Shift Information</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Shift Name:</label>
              <p class="form-control-static"><strong>{{ $shift->name }}</strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Status:</label>
              <p class="form-control-static">
                @if($shift->status == 'Active')
                  <span class="badge badge-success">Active</span>
                @else
                  <span class="badge badge-danger">Inactive</span>
                @endif
              </p>
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
        <h4>Time Settings</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Start Time:</label>
              <p class="form-control-static"><strong>{{ $shift->start_time }}</strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">End Time:</label>
              <p class="form-control-static"><strong>{{ $shift->end_time }}</strong></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Cross Day Shift:</label>
              <p class="form-control-static">
                @if($shift->is_cross_day)
                  <span class="badge badge-warning">Yes</span>
                @else
                  <span class="badge badge-secondary">No</span>
                @endif
              </p>
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
        <h4>Grace Periods</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Late Grace Minutes:</label>
              <p class="form-control-static"><strong>{{ $shift->late_grace_minutes }} minutes</strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Early Leave Grace Minutes:</label>
              <p class="form-control-static"><strong>{{ $shift->early_leave_grace_minutes }} minutes</strong></p>
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
        <h4>Auto Checkout</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Auto Checkout After Minutes:</label>
              <p class="form-control-static"><strong>{{ $shift->auto_checkout_after_minutes ?? 'Disabled' }}</strong></p>
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
        <h4>Remarks</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Remarks:</label>
          <p class="form-control-static">{{ $shift->remarks ?? 'N/A' }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Audit Information</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Created By:</label>
              <p class="form-control-static">{{ $shift->creator ? $shift->creator->name : 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Updated By:</label>
              <p class="form-control-static">{{ $shift->updater ? $shift->updater->name : 'N/A' }}</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Created At:</label>
              <p class="form-control-static">{{ $shift->created_at ? $shift->created_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Updated At:</label>
              <p class="form-control-static">{{ $shift->updated_at ? $shift->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-primary">Edit Shift</a>
      <a href="{{ route('shifts.index') }}" class="btn btn-default">Back to List</a>
    </div>
  </div>
</div>

@endsection
