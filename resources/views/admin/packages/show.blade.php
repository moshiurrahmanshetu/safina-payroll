@extends('layouts.admin')
@section('title', 'Package Details')
@section('content')
<h3 class="page-header">
  Package Details
  {{link_to_route('packages.index', 'Back to List', [], array('class'=>'btn btn-success pull-right'))}}
</h3>

<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>{{ $package->name }}</strong>
        <span class="pull-right">
          <span class="label label-{{ $package->status == 1 ? 'success' : 'danger' }}">
            {{ $package->status == 1 ? 'Active' : 'Inactive' }}
          </span>
        </span>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <tr>
            <th style="width: 40%">Base Price</th>
            <td>৳{{ number_format($package->base_price, 2) }}</td>
          </tr>
          <tr>
            <th>Default Person</th>
            <td>{{ $package->default_person }} person(s)</td>
          </tr>
          <tr>
            <th>Extra Person Price</th>
            <td>৳{{ number_format($package->extra_person_price, 2) }}</td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ $package->created_at->format('d-m-Y H:i') }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ $package->updated_at->format('d-m-Y H:i') }}</td>
          </tr>
        </table>

        <h4>Included Tickets</h4>
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Ticket Name</th>
            </tr>
          </thead>
          <tbody>
            @forelse($package->items as $index => $item)
            <tr>
              <td>{{ $index + 1 }}</td>
              {{-- ticket() returns Ticket model via service_id column --}}
              <td>{{ $item->ticket ? $item->ticket->name : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="2" class="text-center text-muted">No tickets included in this package</td>
            </tr>
            @endforelse
          </tbody>
        </table>

        <div class="text-center">
          {!! HTML::decode(link_to_route('packages.edit', '<i class="nav-icon icon-pencil"></i> Edit Package', array($package->id), array('class'=>'btn btn-primary'))) !!}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
