@extends('layouts.admin')
@section('title', 'Ticket Cash Handover')
@section('content')
<h3 class="page-header">Ticket Cash Handover</h3>

<!-- Business Date Filter -->
<div class="row">
  <div class="col-md-12">
    {{ Form::open(array('route' => 'ticket_cash_handovers.index', 'method'=>'GET', 'class'=>'form-inline')) }}
    <div class="form-group" style="margin-right: 10px;">
      <label>Business Date:</label>
      <input type="date" name="business_date" class="form-control" value="{{ $businessDate }}">
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    {{ Form::close() }}
  </div>
</div>
<br>

<!-- Counter-wise Balance Table -->
@if(count($balanceData['counter_wise_balance']) > 0)
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h4>Counter-wise Cash Balance</h4>
      </div>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Counter</th>
              <th>Business Date</th>
              <th>Total Sales</th>
              <th>Pending Amount</th>
              <th>Approved Amount</th>
              <th>Available Amount</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($balanceData['counter_wise_balance'] as $counterBalance)
            <tr>
              <td>{{ $counterBalance['counter_name'] }}</td>
              <td>{{ \Carbon\Carbon::parse($counterBalance['business_date'])->format('d-m-Y') }}</td>
              <td>{{ number_format($counterBalance['total_sales'], 2) }}</td>
              <td>{{ number_format($counterBalance['pending_amount'], 2) }}</td>
              <td>{{ number_format($counterBalance['approved_amount'], 2) }}</td>
              <td><strong>{{ number_format($counterBalance['available_amount'], 2) }}</strong></td>
              <td>
                @if($counterBalance['available_amount'] > 0)
                  <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#handoverModal{{ $counterBalance['counter_id'] }}">
                    Create Handover
                  </button>
                @else
                  <span class="text-muted">No balance</span>
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
@else
<div class="alert alert-info">
  <strong>No Available Balance</strong> You have no available balance to handover. Please make some sales first.
</div>
@endif
<br>

<!-- Pending Handovers -->
@if($balanceData['has_pending_handover'])
<div class="row">
  <div class="col-md-12">
    <div class="card bg-warning">
      <div class="card-header">
        <h4>Pending Handovers</h4>
      </div>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Counter</th>
              <th>Amount</th>
              <th>Business Date</th>
              <th>Requested At</th>
            </tr>
          </thead>
          <tbody>
            @foreach($balanceData['pending_handovers'] as $pendingHandover)
            <tr>
              <td>{{ $pendingHandover->gate->name ?? 'Unknown Gate' }}</td>
              <td>{{ number_format($pendingHandover->amount, 2) }}</td>
              <td>{{ \Carbon\Carbon::parse($pendingHandover->business_date)->format('d-m-Y') }}</td>
              <td>{{ \Carbon\Carbon::parse($pendingHandover->requested_at)->format('d-m-Y (H:i)') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div class="alert alert-warning">
          <strong>Note:</strong> Please wait for manager approval or rejection before creating new handover requests.
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Handover Modals for each counter -->
@foreach($balanceData['counter_wise_balance'] as $counterBalance)
@if($counterBalance['available_amount'] > 0)
<div class="modal fade" id="handoverModal{{ $counterBalance['counter_id'] }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Create Handover Request</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{ Form::open(array('route' => 'ticket_cash_handovers.store', 'method'=>'POST')) }}
        <input type="hidden" name="business_date" value="{{ $businessDate }}">
        <input type="hidden" name="gate_id" value="{{ $counterBalance['counter_id'] }}">
        <input type="hidden" name="amount" value="{{ $counterBalance['available_amount'] }}">
        
        <div class="form-group">
          <label>Counter:</label>
          <p class="form-control-static"><strong>{{ $counterBalance['counter_name'] }}</strong></p>
        </div>
        
        <div class="form-group">
          <label>Business Date:</label>
          <p class="form-control-static">{{date('d-m-Y', strtotime($counterBalance['business_date']))}}</p>
        </div>
        
        <div class="form-group">
          <label>Total Sales:</label>
          <p class="form-control-static">{{ number_format($counterBalance['total_sales'], 2) }}</p>
        </div>
        
        <div class="form-group">
          <label>Pending Amount:</label>
          <p class="form-control-static">{{ number_format($counterBalance['pending_amount'], 2) }}</p>
        </div>
        
        <div class="form-group">
          <label>Approved Amount:</label>
          <p class="form-control-static">{{ number_format($counterBalance['approved_amount'], 2) }}</p>
        </div>
        
        <div class="form-group">
          <label>Handover Amount:</label>
          <p class="form-control-static"><strong>{{ number_format($counterBalance['available_amount'], 2) }}</strong></p>
        </div>
        
        <div class="alert alert-info">
          <strong>Note:</strong> Handover amount must equal the full available balance for this counter. Partial handovers are not allowed.
        </div>
        
        <button type="submit" class="btn btn-success btn-lg">Create Handover Request</button>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
@endif
@endforeach
@endsection
