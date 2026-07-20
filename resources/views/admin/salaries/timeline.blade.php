@extends('layouts.admin')
@section('title', 'Employee Salary Timeline')
@section('content')
<h3 class="page-header">Salary Timeline - {{ $user->name }}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Salary Revision History</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Effective From</th>
                <th>Basic Salary</th>
                <th>Total Salary</th>
                <th>Reason</th>
                <th>Current</th>
                <th>Locked</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
             @php $i=1; @endphp
             @foreach ($salaries as $salary)
             <tr>
              <td>{{$i}}</td>
              <td>{{$salary->effective_from->format('Y-m-d')}}</td>
              <td>{{$salary->basic_salary}}</td>
              <td><strong>{{$salary->total_salary}}</strong></td>
              <td>{{$salary->salary_increment_reason}}</td>
              <td>
                @if($salary->is_current)
                  <span class="badge badge-success">Current</span>
                @else
                  <span class="badge badge-secondary">Old</span>
                @endif
              </td>
              <td>
                @if($salary->is_locked)
                  <span class="badge badge-danger">Locked</span>
                @else
                  <span class="badge badge-secondary">Unlocked</span>
                @endif
              </td>
              <td>{{$salary->creator ? $salary->creator->name : 'N/A'}}</td>
              <td>{{$salary->created_at->format('Y-m-d H:i:s')}}</td>
              <td>
               <a href="{{ route('salaries.show', $salary->id) }}" class="btn btn-info"><i class="nav-icon icon-eye"></i></a>
             </td>
           </tr>
           @php $i=$i+1; @endphp
           @endforeach
         </tbody>
       </table>
     </div>

     <div class="form-group">
       <a href="{{ route('salaries.index') }}" class="btn btn-primary">Back to Salary History</a>
       <a href="{{ route('salaries.create') }}?user_id={{$user->id}}" class="btn btn-success">Create New Revision</a>
     </div>

   </div>
 </div>
</div>
</div>

@endsection
@section('script')

@endsection
