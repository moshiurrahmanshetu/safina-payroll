@extends('layouts.admin')
@section('title', 'Salary Revision Details')
@section('content')
<h3 class="page-header">Salary Revision Details {{link_to_route('salary_revisions.index','Salary Revisions',[],array('class'=>'btn btn-success pull-right'))}}</h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Revision Information</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Employee:</label>
              <p class="form-control-static"><strong>{{ $salary->user ? $salary->user->name : 'N/A' }}</strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Revision Date:</label>
              <p class="form-control-static">{{ $salary->created_at ? $salary->created_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Effective From:</label>
              <p class="form-control-static"><strong>{{ $salary->effective_from->format('Y-m-d') }}</strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Revision Reason:</label>
              <p class="form-control-static"><strong>{{ $salary->salary_increment_reason }}</strong></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Current Status:</label>
              <p class="form-control-static">
                @if($salary->is_current)
                  <span class="badge badge-success">Current</span>
                @else
                  <span class="badge badge-secondary">Old</span>
                @endif
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Locked Status:</label>
              <p class="form-control-static">
                @if($salary->salary_locked)
                  <span class="badge badge-danger">Locked</span>
                @else
                  <span class="badge badge-secondary">Unlocked</span>
                @endif
              </p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Remarks:</label>
              <p class="form-control-static">{{ $salary->remarks ?? 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Salary Comparison -->
@if($previousSalary)
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Salary Comparison (Old vs New)</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Component</th>
                <th>Old Salary</th>
                <th>New Salary</th>
                <th>Difference</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Basic Salary</strong></td>
                <td>{{ $previousSalary->basic_salary }}</td>
                <td><strong>{{ $salary->basic_salary }}</strong></td>
                <td>
                  @php
                    $diff = $salary->basic_salary - $previousSalary->basic_salary;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>House Rent</td>
                <td>{{ $previousSalary->house_rent }}</td>
                <td>{{ $salary->house_rent }}</td>
                <td>
                  @php
                    $diff = $salary->house_rent - $previousSalary->house_rent;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Medical</td>
                <td>{{ $previousSalary->medical }}</td>
                <td>{{ $salary->medical }}</td>
                <td>
                  @php
                    $diff = $salary->medical - $previousSalary->medical;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Transport</td>
                <td>{{ $previousSalary->transport }}</td>
                <td>{{ $salary->transport }}</td>
                <td>
                  @php
                    $diff = $salary->transport - $previousSalary->transport;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Food</td>
                <td>{{ $previousSalary->food }}</td>
                <td>{{ $salary->food }}</td>
                <td>
                  @php
                    $diff = $salary->food - $previousSalary->food;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Mobile</td>
                <td>{{ $previousSalary->mobile }}</td>
                <td>{{ $salary->mobile }}</td>
                <td>
                  @php
                    $diff = $salary->mobile - $previousSalary->mobile;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Other Allowance</td>
                <td>{{ $previousSalary->other_allowance }}</td>
                <td>{{ $salary->other_allowance }}</td>
                <td>
                  @php
                    $diff = $salary->other_allowance - $previousSalary->other_allowance;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Festival Bonus</td>
                <td>{{ $previousSalary->festival_bonus }}</td>
                <td>{{ $salary->festival_bonus }}</td>
                <td>
                  @php
                    $diff = $salary->festival_bonus - $previousSalary->festival_bonus;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td><strong>Gross Salary</strong></td>
                <td><strong>{{ $previousSalary->gross_salary }}</strong></td>
                <td><strong>{{ $salary->gross_salary }}</strong></td>
                <td>
                  @php
                    $diff = $salary->gross_salary - $previousSalary->gross_salary;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success"><strong>+{{ number_format($diff, 2) }}</strong></span>
                  @elseif($diff < 0)
                    <span class="text-danger"><strong>{{ number_format($diff, 2) }}</strong></span>
                  @else
                    <span class="text-muted"><strong>0.00</strong></span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Late Fine</td>
                <td>{{ $previousSalary->late_fine }}</td>
                <td>{{ $salary->late_fine }}</td>
                <td>
                  @php
                    $diff = $salary->late_fine - $previousSalary->late_fine;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Absent Deduction</td>
                <td>{{ $previousSalary->absent_deduction }}</td>
                <td>{{ $salary->absent_deduction }}</td>
                <td>
                  @php
                    $diff = $salary->absent_deduction - $previousSalary->absent_deduction;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Advance Salary</td>
                <td>{{ $previousSalary->advance_salary }}</td>
                <td>{{ $salary->advance_salary }}</td>
                <td>
                  @php
                    $diff = $salary->advance_salary - $previousSalary->advance_salary;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Tax</td>
                <td>{{ $previousSalary->tax }}</td>
                <td>{{ $salary->tax }}</td>
                <td>
                  @php
                    $diff = $salary->tax - $previousSalary->tax;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>PF</td>
                <td>{{ $previousSalary->pf }}</td>
                <td>{{ $salary->pf }}</td>
                <td>
                  @php
                    $diff = $salary->pf - $previousSalary->pf;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>Other Deduction</td>
                <td>{{ $previousSalary->other_deduction }}</td>
                <td>{{ $salary->other_deduction }}</td>
                <td>
                  @php
                    $diff = $salary->other_deduction - $previousSalary->other_deduction;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success">+{{ number_format($diff, 2) }}</span>
                  @elseif($diff < 0)
                    <span class="text-danger">{{ number_format($diff, 2) }}</span>
                  @else
                    <span class="text-muted">0.00</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td><strong>Net Salary</strong></td>
                <td><strong>{{ $previousSalary->net_salary }}</strong></td>
                <td><strong>{{ $salary->net_salary }}</strong></td>
                <td>
                  @php
                    $diff = $salary->net_salary - $previousSalary->net_salary;
                  @endphp
                  @if($diff > 0)
                    <span class="text-success"><strong>+{{ number_format($diff, 2) }}</strong></span>
                  @elseif($diff < 0)
                    <span class="text-danger"><strong>{{ number_format($diff, 2) }}</strong></span>
                  @else
                    <span class="text-muted"><strong>0.00</strong></span>
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@else
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Salary Details (First Revision)</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Component</th>
                <th>Value</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Basic Salary</strong></td>
                <td><strong>{{ $salary->basic_salary }}</strong></td>
              </tr>
              <tr>
                <td>House Rent</td>
                <td>{{ $salary->house_rent }}</td>
              </tr>
              <tr>
                <td>Medical</td>
                <td>{{ $salary->medical }}</td>
              </tr>
              <tr>
                <td>Transport</td>
                <td>{{ $salary->transport }}</td>
              </tr>
              <tr>
                <td>Food</td>
                <td>{{ $salary->food }}</td>
              </tr>
              <tr>
                <td>Mobile</td>
                <td>{{ $salary->mobile }}</td>
              </tr>
              <tr>
                <td>Other Allowance</td>
                <td>{{ $salary->other_allowance }}</td>
              </tr>
              <tr>
                <td>Festival Bonus</td>
                <td>{{ $salary->festival_bonus }}</td>
              </tr>
              <tr>
                <td><strong>Gross Salary</strong></td>
                <td><strong>{{ $salary->gross_salary }}</strong></td>
              </tr>
              <tr>
                <td>Late Fine</td>
                <td>{{ $salary->late_fine }}</td>
              </tr>
              <tr>
                <td>Absent Deduction</td>
                <td>{{ $salary->absent_deduction }}</td>
              </tr>
              <tr>
                <td>Advance Salary</td>
                <td>{{ $salary->advance_salary }}</td>
              </tr>
              <tr>
                <td>Tax</td>
                <td>{{ $salary->tax }}</td>
              </tr>
              <tr>
                <td>PF</td>
                <td>{{ $salary->pf }}</td>
              </tr>
              <tr>
                <td>Other Deduction</td>
                <td>{{ $salary->other_deduction }}</td>
              </tr>
              <tr>
                <td><strong>Net Salary</strong></td>
                <td><strong>{{ $salary->net_salary }}</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Audit Information -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Audit Information</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Created By:</label>
              <p class="form-control-static">{{ $salary->creator ? $salary->creator->name : 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Created At:</label>
              <p class="form-control-static">{{ $salary->created_at ? $salary->created_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Updated By:</label>
              <p class="form-control-static">{{ $salary->updater ? $salary->updater->name : 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Updated At:</label>
              <p class="form-control-static">{{ $salary->updated_at ? $salary->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
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
      {!! HTML::decode(link_to_route('salary_revisions.index', '<i class="nav-icon icon-arrow-left"></i> Back', [], array('class' => 'btn btn-default'))) !!}
    </div>
  </div>
</div>

@endsection
@section('script')

@endsection
