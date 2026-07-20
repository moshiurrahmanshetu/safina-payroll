@extends('layouts.admin')
@section('title', 'Create Salary Revision')
@section('content')
<h3 class="page-header">Create Salary Revision {{link_to_route('salary_revisions.index','Salary Revisions',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>New Salary Revision</h4>
      </div>
      <div class="panel-body">
        {{ Form::open(array('route' => 'salary_revisions.store', 'method'=>'POST', 'class'=>'form-horizontal')) }}
        
        <div class="form-group">
          <label class="control-label col-md-3">Employee *</label>
          <div class="col-md-9">
            <select name="user_id" class="form-control" id="user_id" required>
              <option value="">Select Employee</option>
              @if($users->count() > 0)
                @foreach($users as $id => $name)
                  <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
              @else
                <option value="" disabled>No eligible employees found</option>
              @endif
            </select>
            {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
          </div>
        </div>

        <!-- Current Salary Information -->
        <div class="form-group">
          <div class="col-md-12">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h4>Current Salary Information</h4>
              </div>
              <div class="panel-body" id="currentSalaryInfo">
                <p class="text-muted">Select an employee to view current salary information</p>
              </div>
            </div>
          </div>
        </div>

        <hr>
        <h4>New Salary Components</h4>

        <div class="form-group">
          <label class="control-label col-md-3">Basic Salary *</label>
          <div class="col-md-9">
            <input type="number" name="basic_salary" class="form-control" step="0.01" min="0" required>
            {!! $errors->first('basic_salary', '<p class="text-danger">:message</p>') !!}
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">House Rent</label>
          <div class="col-md-9">
            <input type="number" name="house_rent" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Medical</label>
          <div class="col-md-9">
            <input type="number" name="medical" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Transport</label>
          <div class="col-md-9">
            <input type="number" name="transport" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Food</label>
          <div class="col-md-9">
            <input type="number" name="food" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Mobile</label>
          <div class="col-md-9">
            <input type="number" name="mobile" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Other Allowance</label>
          <div class="col-md-9">
            <input type="number" name="other_allowance" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Festival Bonus</label>
          <div class="col-md-9">
            <input type="number" name="festival_bonus" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <hr>
        <h4>Deductions</h4>

        <div class="form-group">
          <label class="control-label col-md-3">Late Fine</label>
          <div class="col-md-9">
            <input type="number" name="late_fine" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Absent Deduction</label>
          <div class="col-md-9">
            <input type="number" name="absent_deduction" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Advance Salary</label>
          <div class="col-md-9">
            <input type="number" name="advance_salary" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Tax</label>
          <div class="col-md-9">
            <input type="number" name="tax" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">PF</label>
          <div class="col-md-9">
            <input type="number" name="pf" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Other Deduction</label>
          <div class="col-md-9">
            <input type="number" name="other_deduction" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>

        <hr>
        <h4>Revision Details</h4>

        <div class="form-group">
          <label class="control-label col-md-3">Effective From *</label>
          <div class="col-md-9">
            <input type="date" name="effective_from" class="form-control" required>
            {!! $errors->first('effective_from', '<p class="text-danger">:message</p>') !!}
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Revision Reason *</label>
          <div class="col-md-9">
            <select name="revision_reason" class="form-control" required>
              <option value="">Select Reason</option>
              <option value="Annual Increment">Annual Increment</option>
              <option value="Promotion">Promotion</option>
              <option value="Salary Adjustment">Salary Adjustment</option>
              <option value="Department Change">Department Change</option>
              <option value="Performance Bonus">Performance Bonus</option>
              <option value="Correction">Correction</option>
              <option value="Other">Other</option>
            </select>
            {!! $errors->first('revision_reason', '<p class="text-danger">:message</p>') !!}
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Salary Lock</label>
          <div class="col-md-9">
            <select name="salary_locked" class="form-control">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
            <small class="text-muted">If set to Yes, no further revisions will be allowed until unlocked</small>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Remarks</label>
          <div class="col-md-9">
            <textarea name="remarks" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-9 col-md-offset-3">
            <button type="submit" class="btn btn-primary">Save Salary Revision</button>
            <a href="{{ route('salary_revisions.index') }}" class="btn btn-danger">Cancel</a>
          </div>
        </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
<script>
$(document).ready(function() {
  $('#user_id').change(function() {
    const userId = $(this).val();
    
    if (!userId) {
      $('#currentSalaryInfo').html('<p class="text-muted">Select an employee to view current salary information</p>');
      return;
    }

    // Show loading
    $('#currentSalaryInfo').html('<p class="text-muted">Loading...</p>');

    // AJAX to get current salary
    $.ajax({
      url: '/api/salaries/current/' + userId,
      type: 'GET',
      success: function(response) {
        if (response.current_salary) {
          const salary = response.current_salary;
          const html = `
            <div class="row">
              <div class="col-md-6">
                <strong>Basic Salary:</strong> ${salary.basic_salary}<br>
                <strong>House Rent:</strong> ${salary.house_rent}<br>
                <strong>Medical:</strong> ${salary.medical}<br>
                <strong>Transport:</strong> ${salary.transport}<br>
                <strong>Food:</strong> ${salary.food}<br>
                <strong>Mobile:</strong> ${salary.mobile}<br>
                <strong>Other Allowance:</strong> ${salary.other_allowance}
              </div>
              <div class="col-md-6">
                <strong>Festival Bonus:</strong> ${salary.festival_bonus}<br>
                <strong>Late Fine:</strong> ${salary.late_fine}<br>
                <strong>Absent Deduction:</strong> ${salary.absent_deduction}<br>
                <strong>Advance Salary:</strong> ${salary.advance_salary}<br>
                <strong>Tax:</strong> ${salary.tax}<br>
                <strong>PF:</strong> ${salary.pf}<br>
                <strong>Other Deduction:</strong> ${salary.other_deduction}
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-md-6">
                <strong>Gross Salary:</strong> ${salary.gross_salary}<br>
                <strong>Net Salary:</strong> ${salary.net_salary}
              </div>
              <div class="col-md-6">
                <strong>Effective From:</strong> ${salary.effective_from}<br>
                <strong>Locked:</strong> ${salary.salary_locked ? 'Yes' : 'No'}
              </div>
            </div>
          `;
          $('#currentSalaryInfo').html(html);
        } else {
          $('#currentSalaryInfo').html('<p class="text-warning">No current salary found for this employee</p>');
        }
      },
      error: function() {
        $('#currentSalaryInfo').html('<p class="text-danger">Error loading current salary</p>');
      }
    });
  });
});
</script>
@endsection
