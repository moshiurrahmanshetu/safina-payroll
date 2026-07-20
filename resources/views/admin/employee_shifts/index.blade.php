@extends('layouts.admin')
@section('title', 'Employee Shift Assignment')
@section('content')
<h3 class="page-header">Employee Shift Assignment</h3>

<div class="row">
  <div class="col-md-12">
    <div class="text-right mb-3">
      <a href="{{ route('employee_shifts.create') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> Assign Shift
      </a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Shift</th>
            <th>Effective From</th>
            <th>Effective To</th>
            <th>Default</th>
            <th>Status</th>
            <th>Current Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($employeeShifts as $employeeShift)
            <tr>
              <td>{{ $employeeShift->user ? $employeeShift->user->name : 'N/A' }}</td>
              <td>{{ $employeeShift->shift ? $employeeShift->shift->name : 'N/A' }}</td>
              <td>{{ $employeeShift->effective_from }}</td>
              <td>{{ $employeeShift->effective_to ?? 'Ongoing' }}</td>
              <td>{{ $employeeShift->is_default ? 'Yes' : 'No' }}</td>
              <td>{{ $employeeShift->status }}</td>
              <td>{!! $employeeShift->getCurrentStatusBadge() !!}</td>
              <td>
                <a href="{{ route('employee_shifts.show', $employeeShift->id) }}" class="btn btn-sm btn-info">
                  <i class="fa fa-eye"></i>
                </a>
                <a href="{{ route('employee_shifts.edit', $employeeShift->id) }}" class="btn btn-sm btn-warning">
                  <i class="fa fa-edit"></i>
                </a>
                <form action="{{ route('employee_shifts.destroy', $employeeShift->id) }}" method="POST" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to deactivate this shift assignment?')">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
