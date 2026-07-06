@extends('layouts.admin')
@section('title', 'Contract Worker List')
@section('content')
<h3 class="page-header">Contract Worker List @if($contract_workers) ({{count($contract_workers)}}) @endif {{link_to_route('contract_workers.create','Add Contract Worker',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'contract_workers.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, Worker ID, Mobile">
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>Work Area:</label>
          <select name="work_area_id" class="form-control">
            <option value="">All Work Areas</option>
            @foreach($work_areas as $id => $name)
              <option value="{{ $id }}" {{ request('work_area_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Completed</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('contract_workers.index') }}" class="btn btn-danger">Reset</a>
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
            <th>Worker ID</th>
            <th>Photo</th>
            <th>Full Name</th>
            <th>Mobile</th>
            <th>Work Area</th>
            <th>Contract Title</th>
            <th>Contract Amount</th>
            <th>Advance Amount</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($contract_workers as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->contract_worker_id}}</strong></td>
          <td>
            @if($data->photo)
              <img src="{{ asset($data->photo) }}" alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            @else
              <span class="text-muted">No Photo</span>
            @endif
          </td>
          <td>{{$data->full_name}}</td>
          <td>{{$data->mobile}}</td>
          <td>{{$data->workArea ? $data->workArea->name : 'N/A'}}</td>
          <td>{{$data->contract_title}}</td>
          <td>{{$data->contract_amount}}</td>
          <td>{{$data->advance_amount}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
           {!! HTML::decode(link_to_route('contract_workers.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('contract_workers.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
           <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
           {{ Form::close() }}
         </td>
       </tr>
       @php $i=$i+1; @endphp
       @endforeach
     </tbody>
   </table>
 </div>

</div>
</div>

@endsection
@section('script')

@endsection
