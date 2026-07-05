@extends('layouts.admin')
@section('title', 'Locker Items')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-lock mr-2"></i>Locker Items @if($lockers) ({{count($lockers)}}) @endif</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Locker Items</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card card-outline card-primary shadow">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>Locker List @if($lockers) ({{count($lockers)}}) @endif</h3>
    <div class="card-tools">
      @if(checkMenuActive('LockerItemController@create', $menu_list))
      <a href="{{ route('locker_items.create') }}" class="btn btn-success btn-sm">
        <i class="fa fa-plus-circle mr-1"></i> Create Locker
      </a>
      @endif
    </div>
  </div>

  <div class="card-body p-0">
    @if(session('flash_success'))
    <div class="alert alert-success m-3">
      <i class="fa fa-check-circle mr-2"></i>{{ session('flash_success') }}
    </div>
    @endif

    @if(session('flash_error'))
    <div class="alert alert-danger m-3">
      <i class="fa fa-exclamation-circle mr-2"></i>{{ session('flash_error') }}
    </div>
    @endif

    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lockers as $locker)
          <tr>
            <td>{{ $locker->id }}</td>
            <td><strong>{{ $locker->name }}</strong></td>
            <td>
              @if($locker->status == 'available')
                <span class="badge badge-success">Available</span>
              @else
                <span class="badge badge-danger">Occupied</span>
              @endif
            </td>
            <td>
              @if(checkMenuActive('LockerItemController@edit', $menu_list))
              <a href="{{ route('locker_items.edit', $locker->id) }}" class="btn btn-primary btn-sm">
                <i class="fa fa-edit"></i> Edit
              </a>
              @endif
              @if(checkMenuActive('LockerItemController@destroy', $menu_list))
              {{ Form::open(['route' => ['locker_items.destroy', $locker->id], 'method' => 'DELETE', 'style' => 'display:inline']) }}
              <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                <i class="fa fa-trash"></i> Delete
              </button>
              {{ Form::close() }}
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center text-muted py-4">
              <i class="fa fa-inbox fa-2x mb-2"></i><br>
              No lockers found
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
