@extends('layouts.admin')
@section('title', 'Packages')
@section('content')
<h3 class="page-header">
  Package Management @if($packages) ({{count($packages)}}) @endif
  {{link_to_route('packages.create','Add Package',[],array('class'=>'btn btn-success pull-right'))}}
</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Base Price</th>
            <th>Default Person</th>
            <th>Extra Person Price</th>
            <th>Tickets</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @forelse($packages as $package)
         <tr>
          <td>{{$i}}</td>
          <td>{{$package->name}}</td>
          <td>৳{{number_format($package->base_price, 2)}}</td>
          <td>{{$package->default_person}}</td>
          <td>৳{{number_format($package->extra_person_price, 2)}}</td>
          <td>
            @if($package->items->count() > 0)
              <span class="badge badge-info">{{$package->items->count()}} tickets</span>
            @else
              <span class="text-muted">No tickets</span>
            @endif
          </td>
          <td>
            <span class="label label-{{ $package->status == 1 ? 'success' : 'danger' }}">
              {{ $package->status == 1 ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td>
            {!! HTML::decode(link_to_route('packages.show', '<i class="nav-icon icon-eye"></i>', array($package->id), array('class'=>'btn btn-info btn-sm')))!!}
            {!! HTML::decode(link_to_route('packages.edit', '<i class="nav-icon icon-pencil"></i>', array($package->id), array('class'=>'btn btn-primary btn-sm')))!!}
            {{ Form::open(array('route' => array('packages.destroy', $package->id), 'method'=>'DELETE', 'id'=>'del-form-'.$package->id, 'style'=>'display:inline')) }}
            <button type="submit" class="btn btn-danger btn-sm delete-form" onclick="return confirm('Are you sure you want to delete this package?')">
              <i class="nav-icon icon-trash"></i>
            </button>
            {{ Form::close() }}
          </td>
        </tr>
        @php $i=$i+1; @endphp
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted">No packages found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
</div>

@endsection
