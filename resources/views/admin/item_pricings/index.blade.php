@extends('layouts.admin')
@section('title', 'Item Pricing')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fa fa-tags mr-2"></i>Item Pricing @if($pricings) ({{count($pricings)}}) @endif</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Item Pricing</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card card-outline card-primary shadow">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-table mr-2"></i>Pricing List @if($pricings) ({{count($pricings)}}) @endif</h3>
    <div class="card-tools">
      @if(checkMenuActive('ItemPricingController@create', $menu_list))
      <a href="{{ route('item_pricings.create') }}" class="btn btn-success btn-sm">
        <i class="fa fa-plus-circle mr-1"></i> Create Pricing
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
            <th>Item Type</th>
            <th>Item Name</th>
            <th>Duration</th>
            <th>Base Price</th>
            <th>Extra Unit</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pricings as $pricing)
          <tr>
            <td>{{ $pricing->id }}</td>
            <td>
              @if($pricing->item_type == 'locker')
                <span class="badge badge-info"><i class="fa fa-lock mr-1"></i> Locker</span>
              @else
                <span class="badge badge-warning"><i class="fa fa-tshirt mr-1"></i> Gear</span>
              @endif
            </td>
            <td>
              <strong>
                @if($pricing->item_type == 'locker' && is_null($pricing->item_id))
                  <span class="text-success"><i class="fa fa-globe mr-1"></i>All Lockers</span>
                @else
                  {{ $pricing->item->name ?? 'N/A' }}
                @endif
              </strong>
            </td>
            <td>{{ $pricing->duration_minutes }} min</td>
            <td>{{ number_format($pricing->base_price, 2) }} Tk</td>
            <td>
              {{ $pricing->extra_unit_minutes }} min / {{ number_format($pricing->extra_unit_price, 2) }} Tk
            </td>
            <td>
              @if(checkMenuActive('ItemPricingController@edit', $menu_list))
              <a href="{{ route('item_pricings.edit', $pricing->id) }}" class="btn btn-primary btn-sm">
                <i class="fa fa-edit"></i> Edit
              </a>
              @endif
              @if(checkMenuActive('ItemPricingController@destroy', $menu_list))
              {{ Form::open(['route' => ['item_pricings.destroy', $pricing->id], 'method' => 'DELETE', 'style' => 'display:inline']) }}
              <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                <i class="fa fa-trash"></i> Delete
              </button>
              {{ Form::close() }}
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <i class="fa fa-inbox fa-2x mb-2"></i><br>
              No pricing rules found
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
