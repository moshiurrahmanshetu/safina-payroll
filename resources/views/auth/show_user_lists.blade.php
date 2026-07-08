@extends('layouts.admin')
@section('title', 'User Lists')
@section('content')
<h1 class="page-header">User Lists {{link_to_route('users.create','Add User',[],array('class'=>'btn btn-success pull-right'))}}</h1> 
{{ session()->get('langsname') }}
<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>SI#</th>
            <th>Name</th>
            <th>Photo</th>
            <th>Designation</th>
            <th>Department</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Role</th>
            <th>Status</th>
            <th>Salary Processing</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp
          @foreach ($users as $data) 
          <tr>
            <td>{{$i}}</td>
            <td>{{$data->name}}</td>
            <td>{{ HTML::image('storage/app/admin/users/'.$data->photo, null, array('width'=>'70', 'class'=>'img-responsive')) }}</td>
            <td>{{$data->designation->name}}</td>
            <td>{{$data->department->name}}</td>
            <td>{{$data->email}}</td>
            <td>{{$data->mobile_no}}</td>
            <td>{{$data->role->name}}</td>

            <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
            <td><strong class="btn-{{ config('myhelpers.salary_processing_color')[$data->salary_processing ?? 0] }}">{{config('myhelpers.salary_processing')[$data->salary_processing ?? 0]}}</strong></td>
            <td> 
              {!! HTML::decode(link_to_route('users.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
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