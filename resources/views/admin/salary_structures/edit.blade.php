@extends('layouts.admin')
@section('title', 'Salary Structure Edit')
@section('content')
<h3 class="page-header">Salary Structure Edit {{link_to_route('salary_structures.index','Salary Structure List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model($salary_structure,array('route' => array('salary_structures.update', $salary_structure->id),'enctype'=>'multipart/form-data','method'=>'PUT','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Employee</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Employee *</label>
          <input type="text" class="form-control" value="{{ $salary_structure->user ? $salary_structure->user->name : 'N/A' }}" readonly>
          {{ Form::hidden('user_id') }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Earnings (Allowances)</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Basic Salary *</label>
              {{Form::number('basic_salary',null, array('class' => 'form-control', 'required'=>'required', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('basic_salary', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">House Rent</label>
              {{Form::number('house_rent',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('house_rent', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Medical Allowance</label>
              {{Form::number('medical',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('medical', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>

        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Transport Allowance</label>
              {{Form::number('transport',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('transport', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Food Allowance</label>
              {{Form::number('food',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('food', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Mobile Allowance</label>
              {{Form::number('mobile',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('mobile', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>

        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Other Allowance</label>
              {{Form::number('other_allowance',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('other_allowance', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Festival Bonus</label>
              {{Form::number('festival_bonus',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('festival_bonus', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Deductions</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Late Fine</label>
              {{Form::number('late_fine',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('late_fine', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Absent Deduction</label>
              {{Form::number('absent_deduction',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('absent_deduction', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Advance Salary</label>
              {{Form::number('advance_salary',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('advance_salary', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>

        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Tax</label>
              {{Form::number('tax',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('tax', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Provident Fund (PF)</label>
              {{Form::number('pf',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('pf', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Other Deduction</label>
              {{Form::number('other_deduction',null, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0'))}}
              {!! $errors->first('other_deduction', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Status</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Status *</label>
              {{Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control', 'required'=>'required'))}}
              {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <button type="submit" class="btn btn-primary">
        Update Salary Structure
      </button>
    </div>
  </div>
</div>
{{ Form::close() }}
</div>

@endsection
@section('script')

@endsection
