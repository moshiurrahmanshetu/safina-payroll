@extends('layouts.admin')
@section('title', 'Payroll Generate')
@section('content')
<h3 class="page-header">Payroll Generate {{link_to_route('payrolls.index','Payroll List',[],array('class'=>'btn btn-success pull-right'))}}</h3>

{{ Form::model(Request::old(),array('route' => array('payrolls.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Select Employee</h4>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label class="control-label">Employee *</label>
          <select name="user_id" class="form-control" id="user_id" required onchange="calculateGeneratedSalary();">
            <option value="">Select Employee</option>
            @if($users->count() > 0)
              @foreach($users as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            @else
              <option value="" disabled>No eligible employees found (users with salary_processing=1 and current salary)</option>
            @endif
          </select>
          {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Payroll Details</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Payroll Month *</label>
              <input type="month" name="payroll_month" class="form-control" id="payroll_month" required onchange="calculateGeneratedSalary();">
              {!! $errors->first('payroll_month', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Generated Salary *</label>
              {{Form::number('generated_salary',null, array('class' => 'form-control', 'required'=>'required', 'step'=>'0.01', 'min'=>'0', 'readonly'=>'readonly', 'id'=>'generated_salary'))}}
              {!! $errors->first('generated_salary', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>

        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Bonus</label>
              {{Form::number('bonus',0, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0', 'id'=>'bonus', 'onchange'=>'calculateGeneratedSalary();'))}}
              {!! $errors->first('bonus', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Deduction</label>
              {{Form::number('deduction',0, array('class' => 'form-control', 'step'=>'0.01', 'min'=>'0', 'id'=>'deduction', 'onchange'=>'calculateGeneratedSalary();'))}}
              {!! $errors->first('deduction', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
        </div>

        <div class="col-md-12 multi-column">
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Status *</label>
              <select name="status" class="form-control" required>
                <option value="0">Draft</option>
                <option value="1">Sent To Manager</option>
                <option value="2">Approved</option>
                <option value="3">Rejected</option>
              </select>
              {!! $errors->first('status', '<p class="text-danger">:message</p>') !!}
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <label class="control-label">Remarks</label>
              {{Form::textarea('remarks',null, array('class' => 'form-control', 'rows'=>'3'))}}
              {!! $errors->first('remarks', '<p class="text-danger">:message</p>') !!}
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
        <h4>Attendance Summary</h4>
      </div>
      <div class="panel-body">
        <div class="col-md-12 multi-column">
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Present</label>
              <input type="text" class="form-control" id="summary_present" readonly value="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Late</label>
              <input type="text" class="form-control" id="summary_late" readonly value="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Half Day</label>
              <input type="text" class="form-control" id="summary_half_day" readonly value="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Absent</label>
              <input type="text" class="form-control" id="summary_absent" readonly value="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Leave</label>
              <input type="text" class="form-control" id="summary_leave" readonly value="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Holiday</label>
              <input type="text" class="form-control" id="summary_holiday" readonly value="0">
            </div>
          </div>
        </div>
        <div class="col-md-12 multi-column" style="margin-top: 10px;">
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Weekly Off</label>
              <input type="text" class="form-control" id="summary_weekly_off" readonly value="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Late Deduction</label>
              <input type="text" class="form-control" id="late_deduction" readonly value="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Absent Deduction</label>
              <input type="text" class="form-control" id="absent_deduction" readonly value="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Effective Absent</label>
              <input type="text" class="form-control" id="effective_absent" readonly value="0">
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
        Generate Payroll
      </button>
    </div>
  </div>
</div>
{{ Form::close() }}
</div>

@endsection
@section('script')
<script>
function calculateGeneratedSalary() {
  var userId = document.getElementById('user_id').value;
  var payrollMonth = document.getElementById('payroll_month').value;
  var bonus = document.getElementById('bonus').value;
  var deduction = document.getElementById('deduction').value;

  if (userId && payrollMonth) {
    var formData = new FormData();
    formData.append('user_id', userId);
    formData.append('payroll_month', payrollMonth);
    formData.append('bonus', bonus || 0);
    formData.append('deduction', deduction || 0);

    var url = "{{ route('payrolls.calculate_generated_salary') }}";

    fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.generated_salary) {
        document.getElementById('generated_salary').value = data.generated_salary;
      }
      if (data.attendance_summary) {
        document.getElementById('summary_present').value = data.attendance_summary['Present'];
        document.getElementById('summary_late').value = data.attendance_summary['Late'];
        document.getElementById('summary_half_day').value = data.attendance_summary['Half Day'];
        document.getElementById('summary_absent').value = data.attendance_summary['Absent'];
        document.getElementById('summary_leave').value = data.attendance_summary['Leave'];
        document.getElementById('summary_holiday').value = data.attendance_summary['Holiday'];
        document.getElementById('summary_weekly_off').value = data.attendance_summary['Weekly Off'];
      }
      if (data.late_deduction) {
        document.getElementById('late_deduction').value = data.late_deduction;
      }
      if (data.absent_deduction) {
        document.getElementById('absent_deduction').value = data.absent_deduction;
      }
      if (data.effective_absent) {
        document.getElementById('effective_absent').value = data.effective_absent;
      }
    })
    .catch(error => console.error('Error:', error));
  }
}
</script>
@endsection
