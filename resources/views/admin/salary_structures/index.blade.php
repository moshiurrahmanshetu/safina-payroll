@extends('layouts.admin')
@section('title', 'Salary Structure List')
@section('content')
<h3 class="page-header">Salary Structure List @if($salary_structures) ({{count($salary_structures)}}) @endif {{link_to_route('salary_structures.create','Add Salary Structure',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'salary_structures.index', 'method'=>'GET', 'class'=>'form-horizontal')) }}
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Employee Name">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('salary_structures.index') }}" class="btn btn-danger">Reset</a>
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
            <th>Employee</th>
            <th>Basic Salary</th>
            <th>Total Allowances</th>
            <th>Total Deductions</th>
            <th>Net Salary</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($salary_structures as $data)
         <tr>
          <td>{{$i}}</td>
          <td><strong>{{$data->user ? $data->user->name : 'N/A'}}</strong></td>
          <td>{{$data->basic_salary}}</td>
          <td>{{$data->house_rent + $data->medical + $data->transport + $data->food + $data->mobile + $data->other_allowance + $data->festival_bonus}}</td>
          <td>{{$data->late_fine + $data->absent_deduction + $data->advance_salary + $data->tax + $data->pf + $data->other_deduction}}</td>
          <td><strong>{{$data->basic_salary + $data->house_rent + $data->medical + $data->transport + $data->food + $data->mobile + $data->other_allowance + $data->festival_bonus - $data->late_fine - $data->absent_deduction - $data->advance_salary - $data->tax - $data->pf - $data->other_deduction}}</strong></td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
           {!! HTML::decode(link_to_route('salary_structures.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('salary_structures.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
