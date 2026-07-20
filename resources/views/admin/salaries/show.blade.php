@extends('layouts.admin')
@section('title', 'View Salary History')
@section('content')
<h3 class="page-header">Salary History Details</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Salary Information</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Employee:</label>
              <input type="text" class="form-control" value="{{ $salary->user ? $salary->user->name : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Effective From:</label>
              <input type="text" class="form-control" value="{{ $salary->effective_from->format('Y-m-d') }}" readonly>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Salary Increment Reason:</label>
              <input type="text" class="form-control" value="{{ $salary->salary_increment_reason }}" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Remarks:</label>
              <textarea class="form-control" rows="2" readonly>{{ $salary->remarks ?? 'N/A' }}</textarea>
            </div>
          </div>
        </div>

        <hr>
        <h4>Salary Components</h4>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Basic Salary:</label>
              <input type="text" class="form-control" value="{{ $salary->basic_salary }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>House Rent:</label>
              <input type="text" class="form-control" value="{{ $salary->house_rent }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Medical:</label>
              <input type="text" class="form-control" value="{{ $salary->medical }}" readonly>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Transport:</label>
              <input type="text" class="form-control" value="{{ $salary->transport }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Food:</label>
              <input type="text" class="form-control" value="{{ $salary->food }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Mobile:</label>
              <input type="text" class="form-control" value="{{ $salary->mobile }}" readonly>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Other Allowance:</label>
              <input type="text" class="form-control" value="{{ $salary->other_allowance }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Festival Bonus:</label>
              <input type="text" class="form-control" value="{{ $salary->festival_bonus }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Total Salary:</label>
              <input type="text" class="form-control" value="{{ $salary->total_salary }}" readonly style="font-weight: bold;">
            </div>
          </div>
        </div>

        <hr>
        <h4>Deductions</h4>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Late Fine:</label>
              <input type="text" class="form-control" value="{{ $salary->late_fine }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Absent Deduction:</label>
              <input type="text" class="form-control" value="{{ $salary->absent_deduction }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Advance Salary:</label>
              <input type="text" class="form-control" value="{{ $salary->advance_salary }}" readonly>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Tax:</label>
              <input type="text" class="form-control" value="{{ $salary->tax }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>PF:</label>
              <input type="text" class="form-control" value="{{ $salary->pf }}" readonly>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Other Deduction:</label>
              <input type="text" class="form-control" value="{{ $salary->other_deduction }}" readonly>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Total Deduction:</label>
              <input type="text" class="form-control" value="{{ $salary->total_deduction }}" readonly style="font-weight: bold;">
            </div>
          </div>
        </div>

        <hr>
        <h4>Status Information</h4>

        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Current:</label>
              @if($salary->is_current)
                <span class="badge badge-success">Yes</span>
              @else
                <span class="badge badge-secondary">No</span>
              @endif
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Locked:</label>
              @if($salary->is_locked)
                <span class="badge badge-danger">Yes</span>
              @else
                <span class="badge badge-secondary">No</span>
              @endif
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Status:</label>
              @if($salary->status)
                <span class="badge badge-success">Active</span>
              @else
                <span class="badge badge-danger">Inactive</span>
              @endif
            </div>
          </div>
        </div>

        <hr>
        <h4>Audit Information</h4>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Created By:</label>
              <input type="text" class="form-control" value="{{ $salary->creator ? $salary->creator->name : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Created At:</label>
              <input type="text" class="form-control" value="{{ $salary->created_at->format('Y-m-d H:i:s') }}" readonly>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Updated By:</label>
              <input type="text" class="form-control" value="{{ $salary->updater ? $salary->updater->name : 'N/A' }}" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Updated At:</label>
              <input type="text" class="form-control" value="{{ $salary->updated_at->format('Y-m-d H:i:s') }}" readonly>
            </div>
          </div>
        </div>

        <div class="form-group">
          <a href="{{ route('salaries.index') }}" class="btn btn-primary">Back to List</a>
          @if(!$salary->is_locked)
            <a href="{{ route('salaries.edit', $salary->id) }}" class="btn btn-info">Edit</a>
          @endif
          <a href="{{ route('salaries.timeline', $salary->user_id) }}" class="btn btn-success">View Timeline</a>
        </div>

      </div>
    </div>
  </div>
</div>

@endsection
@section('script')

@endsection
