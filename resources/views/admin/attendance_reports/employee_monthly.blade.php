@extends('adminlte::page')

@section('title', 'Employee Monthly Attendance Report')

@section('content_header')
    <h1>Employee Monthly Attendance Report</h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('attendance_reports.index') }}">Attendance Reports</a></li>
        <li class="active">Employee Monthly Attendance</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Filter Panel</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Employee</label>
                                <select class="form-control" id="employee_id">
                                    <option value="">-- Select Employee --</option>
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
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Report Output</h3>
                </div>
                <div class="box-body">
                    <p>Report will be displayed here after generation.</p>
                </div>
            </div>
        </div>
    </div>
@stop
