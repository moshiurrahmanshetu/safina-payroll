@extends('layouts.admin')

@section('title', 'Shift Attendance Report')
@section('content')
<h3 class="page-header">Shift Attendance Report {{link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Shift</label>
              <select class="form-control" id="shift_id">
                <option value="">-- Select Shift --</option>
                @foreach($shifts as $shift)
                  <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Month</label>
              <input type="month" class="form-control" id="report_month">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>&nbsp;</label>
              <div>
                <button type="button" class="btn btn-primary" id="generateBtn">
                  <i class="fa fa-file-text"></i> Generate Report
                </button>
                <button type="button" class="btn btn-default" id="printBtn" disabled>
                  <i class="fa fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-default" id="pdfBtn" disabled>
                  <i class="fa fa-file-pdf"></i> Export PDF
                </button>
                <button type="button" class="btn btn-default" id="excelBtn" disabled>
                  <i class="fa fa-file-excel"></i> Export Excel
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row" id="reportSection" style="display: none;">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Report Output</h4>
      </div>
      <div class="panel-body">
        <p>Report will be displayed here after generation.</p>
      </div>
    </div>
  </div>
</div>
@endsection
