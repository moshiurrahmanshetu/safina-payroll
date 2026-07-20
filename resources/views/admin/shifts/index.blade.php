@extends('layouts.admin')
@section('title', 'Shift List')
@section('content')
<h3 class="page-header">Shift List @if($shifts->total() > 0) ({{ $shifts->total() }}) @endif {{link_to_route('shifts.create','Add Shift',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'shifts.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Shift Name">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('shifts.index') }}" class="btn btn-danger">Reset</a>
          </div>
        </div>
      </div>
    </div>
    {{ Form::close() }}
  </div>
</div>
<br>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Shift Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Cross Day</th>
            <th>Late Grace (min)</th>
            <th>Early Leave Grace (min)</th>
            <th>Auto Checkout (min)</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if($shifts->count() > 0)
            @foreach($shifts as $key => $shift)
              <tr>
                <td>{{ ($shifts->currentPage() - 1) * $shifts->perPage() + $key + 1 }}</td>
                <td>{{ $shift->name }}</td>
                <td>{{ $shift->start_time }}</td>
                <td>{{ $shift->end_time }}</td>
                <td>
                  @if($shift->is_cross_day)
                    <span class="badge badge-warning">Yes</span>
                  @else
                    <span class="badge badge-secondary">No</span>
                  @endif
                </td>
                <td>{{ $shift->late_grace_minutes }}</td>
                <td>{{ $shift->early_leave_grace_minutes }}</td>
                <td>{{ $shift->auto_checkout_after_minutes ?? '-' }}</td>
                <td>
                  @if($shift->status == 'Active')
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('shifts.show', $shift->id) }}" class="btn btn-info btn-sm" title="View">
                    <i class="nav-icon icon-eye"></i>
                  </a>
                  <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-primary btn-sm" title="Edit">
                    <i class="nav-icon icon-pencil"></i>
                  </a>
                  <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this shift?')">
                      <i class="nav-icon icon-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="10" class="text-center">No shifts found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($shifts->hasPages())
  <div class="row">
    <div class="col-md-12">
      {{ $shifts->links() }}
    </div>
  </div>
@endif

@endsection
