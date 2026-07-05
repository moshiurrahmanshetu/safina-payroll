@extends('layouts.admin')
@section('title', 'Service Details')
@section('content')
<h3 class="page-header">Service Details {{link_to_route('services.index','Service List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{{$services->name}}</h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Category:</strong> {{$services->service_category ? $services->service_category->name : 'N/A'}}</p>
            <p><strong>Pricing Type:</strong> {{config('myhelpers.pricing_type')[$services->pricing_type]}}</p>
            <p><strong>Status:</strong> <span class="label label-{{ config('myhelpers.status_color')[$services->status] }}">{{config('myhelpers.status')[$services->status]}}</span></p>
          </div>
          <div class="col-md-6">
            <p><strong>Created At:</strong> {{date('d-m-Y h:i A', strtotime($services->created_at))}}</p>
            <p><strong>Updated At:</strong> {{date('d-m-Y h:i A', strtotime($services->updated_at))}}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Service Insights Section -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title">Service Insights</h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$services->total_bookings ?? 0}}</h4>
              <small>Total Bookings</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$services->confirmed_bookings ?? 0}}</h4>
              <small>Confirmed</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$services->pending_bookings ?? 0}}</h4>
              <small>Pending</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>৳{{number_format($services->total_revenue ?? 0, 2)}}</h4>
              <small>Total Revenue</small>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$services->cancelled_bookings ?? 0}}</h4>
              <small>Cancelled</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$services->completed_bookings ?? 0}}</h4>
              <small>Completed</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>{{$services->upcoming_bookings ?? 0}}</h4>
              <small>Upcoming</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h4>৳{{number_format($services->confirmed_bookings > 0 ? ($services->total_revenue / $services->confirmed_bookings) : 0, 2)}}</h4>
              <small>Avg Booking Value</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Most Recent Booking -->
@if($services->bookings && $services->bookings->count() > 0)
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Most Recent Booking</h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Booking ID:</strong> #{{$services->bookings->first()->id}}</p>
            <p><strong>Customer Name:</strong> {{$services->bookings->first()->name}}</p>
            <p><strong>Date:</strong> {{date('d-m-Y', strtotime($services->bookings->first()->check_in_date))}}</p>
          </div>
          <div class="col-md-6">
            <p><strong>Status:</strong> <span class="label label-{{ config('myhelpers.status_color')[$services->bookings->first()->status] }}">{{config('myhelpers.status')[$services->bookings->first()->status]}}</span></p>
            <p><strong>Final Price:</strong> ৳{{number_format($services->bookings->first()->final_price, 2)}}</p>
            <p><strong>Created At:</strong> {{date('d-m-Y h:i A', strtotime($services->bookings->first()->created_at))}}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@else
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <p class="text-muted text-center">No bookings yet for this service.</p>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Customer Information Fields -->
@if($services->meta_fields && $services->meta_fields->count() > 0)
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Customer Information Fields</h3>
      </div>
      <div class="panel-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Field Name</th>
              <th>Field Type</th>
              <th>Required</th>
              <th>Conditional</th>
            </tr>
          </thead>
          <tbody>
            @foreach($services->meta_fields as $field)
            <tr>
              <td>{{$field->field_name}}</td>
              <td>{{config('myhelpers.field_type')[$field->field_type]}}</td>
              <td>{{$field->required ? 'Yes' : 'No'}}</td>
              <td>
                @if($field->conditional_field)
                  <span class="label label-info">{{$field->conditional_field}} = {{$field->conditional_value}}</span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Amenities -->
@if($services->amenities && $services->amenities->count() > 0)
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Amenities</h3>
      </div>
      <div class="panel-body">
        <div class="row">
          @foreach($services->amenities as $amenity)
          <div class="col-md-3">
            <div class="checkbox">
              <label>
                <input type="checkbox" checked disabled> {{$amenity->name}}
              </label>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

@endsection
