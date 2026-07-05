@extends('layouts.admin')
@section('title', 'System User')
@section('content')
<style>.table tr{ border-bottom:1px solid #ddd !important}</style>
<h1 class="page-header">Show User   {{link_to_route('users.index','User Lists',null,array('class'=>'btn btn-success pull-right'))}}  </h1>
      <div class="row">
        <div class="col-sm-12 col-md-6">        
          <table width="100%" class="table">              
              <tr>
                <th width="30%">Full Name</th>
                <td>: {{$users->name}}</td>
              </tr>
              <tr>
                <th>Designation</th>
                <td>: {{$users->designation->name}}</td>
              </tr>
              <tr>
                <th>Role</th>
                <td>: {{$users->role->name}}</td>
              </tr>
              <tr>
                <th>Email</th>
                <td>: {{$users->email}}</td>
              </tr>
             <tr>
                <th>Status</th>
                <td>: {{$status[$users->status]}}</td>
              </tr>
              <tr>
                <th>Created Date Time</th>
                <td>: {{Carbon\Carbon::parse($users->created_at)->format('d-M/Y h:i:s A')}}</td>
              </tr>
              <tr>
                <th>Update Date Time</th>
                <td>: {{Carbon\Carbon::parse($users->updated_at)->format('d-M/Y h:i:s A')}}</td>
              </tr>
              <tr>
              <th>Permission on workstream</th>
              <td>
            @if(count($users->component)>0)
              @foreach ($users->component as $key=>$ele)                 
                {{ Helpers::$workstreams[$ele] }}  
                @if(array_key_exists($ele, Helpers::$workstreams_area)) 
                  ( {{ Helpers::$workstreams_area[$ele] }} );
                @else
                ;
                @endif               
              @endforeach
            @endif
              </td>
              </tr>
              </table>
          </div>  
      </div>
      {{ HTML::decode(link_to_route('users.edit', '<span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Edit Profile', array($users->id),array('class'=>'btn btn-success'))) }}
      {{ link_to_route('getChangePassword','Change Password',array($users->id),array('class'=>'btn btn-success')) }}
@endsection 

