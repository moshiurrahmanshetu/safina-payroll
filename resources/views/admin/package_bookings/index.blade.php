@extends('layouts.admin')
@section('title', 'Package Bookings')
@section('content')
<h3 class="page-header">
  Package Bookings @if($bookings) ({{count($bookings)}}) @endif
  {{link_to_route('package_bookings.create','Add Package Booking',[],array('class'=>'btn btn-success pull-right'))}}
</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Package</th>
            <th>Date</th>
            <th>Qty</th>
            <th>Total Person</th>
            <th>Base</th>
            <th>Extra</th>
            <th>Final Amount</th>
            <th>Package Counter</th>
            <th>Created By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @forelse($bookings as $booking)
         <tr>
          <td>{{$i}}</td>
          <td>{{$booking->package ? $booking->package->name : 'N/A'}}</td>
          <td>{{date('d-m-Y',strtotime($booking->date))}}</td>
          <td>{{$booking->quantity}}</td>
          <td>{{$booking->total_person}}</td>
          <td>৳{{number_format($booking->base_amount, 2)}}</td>
          <td>৳{{number_format($booking->extra_amount, 2)}}</td>
          <td><strong>৳{{number_format($booking->final_amount, 2)}}</strong></td>
          <td>{{$booking->packageCounter ? $booking->packageCounter->name : '-'}}</td>
          <td>{{$booking->creator ? $booking->creator->name : '-'}}</td>
          <td>
            {!! HTML::decode(link_to_route('package_bookings.show', '<i class="nav-icon icon-eye"></i>', array($booking->id), array('class'=>'btn btn-info btn-sm')))!!}
            {{ Form::open(array('route' => array('package_bookings.destroy', $booking->id), 'method'=>'DELETE', 'id'=>'del-form-'.$booking->id, 'style'=>'display:inline')) }}
            <button type="submit" class="btn btn-danger btn-sm delete-form" onclick="return confirm('Are you sure you want to delete this booking?')">
              <i class="nav-icon icon-trash"></i>
            </button>
            {{ Form::close() }}
          </td>
        </tr>
        @php $i=$i+1; @endphp
        @empty
        <tr>
          <td colspan="11" class="text-center text-muted">No package bookings found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
</div>

@endsection
