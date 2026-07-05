@extends('layouts.admin')
@section('title', 'Ticket Counter List')
@section('content')
<h3 class="page-header">Ticket Counter List @if($gates) ({{count($gates)}}) @endif {{link_to_route('gates.create','Add Ticket Counter',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Ticket Counter Name</th>
            <th>Status</th>
            <th>Assigned Users</th>
            <th>Assigned Tickets</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         @php $i=1; @endphp
         @foreach ($gates as $data)
         <tr>
          <td>{{$i}}</td>
          <td>{{$data->name}}</td>
          <td><strong class="btn-{{ config('myhelpers.status_color')[$data->status] }}">{{config('myhelpers.status')[$data->status]}}</strong></td>
          <td>
            @if($data->users->count() > 0)
              @foreach($data->users as $user)
                <span class="badge badge-info">{{$user->name}}</span>
              @endforeach
            @else
              <span class="text-muted">No users assigned</span>
            @endif
          </td>
          <td>
            @if($data->tickets->count() > 0)
              @foreach($data->tickets as $ticket)
                <span class="badge badge-primary">{{$ticket->name}}</span>
              @endforeach
            @else
              <span class="text-muted">No tickets assigned</span>
            @endif
          </td>
          <td>{{date('d-m-Y',strtotime($data->created_at))}}</td>
          <td>
           {!! HTML::decode(link_to_route('gates.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id)))!!}
           {{ Form::open(array('route' => array('gates.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form')) }}
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
