@extends('layouts.admin')
@section('title', 'Create Salary Revision')
@section('content')
<h3 class="page-header">Create Salary Revision</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>New Salary Revision</h4>
      </div>
      <div class="panel-body">
        {{ Form::open(array('route' => 'salaries.store', 'method'=>'POST', 'class'=>'form-horizontal')) }}
        
        <div class="form-group">
          <label class="control-label col-md-3">Employee *</label>
          <div class="col-md-9">
            <select name="user_id" class="form-control" required>
              <option value="">Select Employee</option>
              @foreach($users as $user)
              <option value="{{$user->id}}">{{$user->name}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Effective From *</label>
          <div class="col-md-9">
            <input type="date" name="effective_from" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3">Salary Increment Reason *</label>
          <div class="col-md-9">
            <select name="salary_increment_reason" class="form-control" required>
              <option value="">Select Reason</option>
              <option value="Annual Increment">Annual Increment</option>
              <option value="Promotion">Promotion</option>
              <option value="Salary Adjustment">Salary Adjustment</option>
              <option value="Performance Bonus">Performance Bonus</option>
              <option value="Management Decision">Management Decision</option>
              <option value="Contract Renewal">Contract Renewal</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>

        <hr>
        <h4>Salary Components</h4>

        <div class="form-group">
          <label class="control-label col-md-3">Basic Salary *</label>
          <div class="col-md-9">
            <input type="number" name="basic_salary" class="form-control" step="0.01" min="0" required>
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

        <div class="form-group">
          <label class="control-label col-md-3">Remarks</label>
          <div class="col-md-9">
            <textarea name="remarks" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-9 col-md-offset-3">
            <button type="submit" class="btn btn-primary">Save Salary Revision</button>
            <a href="{{ route('salaries.index') }}" class="btn btn-danger">Cancel</a>
          </div>
        </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')

@endsection
