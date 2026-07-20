@extends('layouts.admin')
@section('title', 'View Employee Shift')
@section('content')
<h3 class="page-header">Employee Shift Details</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-bordered">
              <tr>
                <th style="width: 30%;">Employee</th>
                <td>{{ $employeeShift->user ? $employeeShift->user->name : 'N/A' }}</td>
              </tr>
              <tr>
                <th>Shift</th>
                <td>{{ $employeeShift->shift ? $employeeShift->shift->name : 'N/A' }}</td>
              </tr>
              <tr>
                <th>Effective From</th>
                <td>{{ $employeeShift->effective_from }}</td>
              </tr>
              <tr>
                <th>Effective To</th>
                <td>{{ $employeeShift->effective_to ?? 'Ongoing' }}</td>
              </tr>
              <tr>
                <th>Is Default</th>
                <td>{{ $employeeShift->is_default ? 'Yes' : 'No' }}</td>
              </tr>
              <tr>
                <th>Status</th>
                <td>{{ $employeeShift->status }}</td>
              </tr>
              <tr>
                <th>Current Status</th>
                <td>{!! $employeeShift->getCurrentStatusBadge() !!}</td>
              </tr>
              <tr>
                <th>Remarks</th>
                <td>{{ $employeeShift->remarks ?? '-' }}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-bordered">
              <tr>
                <th style="width: 30%;">Created By</th>
                <td>{{ $employeeShift->creator ? $employeeShift->creator->name : 'N/A' }}</td>
              </tr>
              <tr>
                <th>Created At</th>
                <td>{{ $employeeShift->created_at }}</td>
              </tr>
              <tr>
                <th>Updated By</th>
                <td>{{ $employeeShift->updater ? $employeeShift->updater->name : 'N/A' }}</td>
              </tr>
              <tr>
                <th>Updated At</th>
                <td>{{ $employeeShift->updated_at }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="text-right">
              <a href="{{ route('employee_shifts.edit', $employeeShift->id) }}" class="btn btn-warning">
                <i class="fa fa-edit"></i> Edit
              </a>
              <a href="{{ route('employee_shifts.index') }}" class="btn btn-danger">
                <i class="fa fa-arrow-left"></i> Back
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
