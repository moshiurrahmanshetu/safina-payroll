<?php $__env->startSection('title', 'Employee Daily Attendance Report'); ?>
<?php $__env->startSection('content'); ?>
<h3 class="page-header">Employee Daily Attendance Report <?php echo e(link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))); ?></h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <form method="GET" action="<?php echo e(route('attendance_reports.employee_daily')); ?>">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Employee <span class="text-danger">*</span></label>
                <select class="form-control" name="employee_id" required>
                  <option value="">-- Select Employee --</option>
                  <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($employee->id); ?>" <?php echo e(request('employee_id') == $employee->id ? 'selected' : ''); ?>>
                      <?php echo e($employee->name); ?> (<?php echo e($employee->employee_id ?? $employee->id); ?>)
                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Attendance Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="attendance_date" value="<?php echo e(request('attendance_date')); ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>&nbsp;</label>
                <div>
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-file-text"></i> Generate Report
                  </button>
                  <?php if(isset($employee) && isset($attendanceDate)): ?>
                  <a href="<?php echo e(route('attendance_reports.employee_daily_print', ['employee_id' => $employee->id, 'attendance_date' => $attendanceDate])); ?>" target="_blank" class="btn btn-default">
                    <i class="fa fa-print"></i> Print
                  </a>
                  <?php endif; ?>
                  <button type="button" class="btn btn-default" disabled>
                    <i class="fa fa-file-pdf"></i> Export PDF
                  </button>
                  <button type="button" class="btn btn-default" disabled>
                    <i class="fa fa-file-excel"></i> Export Excel
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if(isset($employee) && isset($attendanceDate)): ?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Report Output</h4>
      </div>
      <div class="panel-body">
        <div class="text-center mb-4">
          <h2><?php echo e($companyName); ?></h2>
          <h3><?php echo e($reportTitle); ?></h3>
        </div>

        <div class="row mb-4">
          <div class="col-md-6">
            <table class="table table-bordered">
              <tr>
                <td><strong>Employee Name:</strong></td>
                <td><?php echo e($employee->name); ?></td>
              </tr>
              <tr>
                <td><strong>Employee ID:</strong></td>
                <td><?php echo e($employee->employee_id ?? $employee->id); ?></td>
              </tr>
              <tr>
                <td><strong>Department:</strong></td>
                <td><?php echo e($employee->department->name ?? 'N/A'); ?></td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-bordered">
              <tr>
                <td><strong>Designation:</strong></td>
                <td><?php echo e($employee->designation->name ?? 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>Assigned Shift:</strong></td>
                <td><?php echo e($attendanceMonthRecord->shift->name ?? 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>Attendance Date:</strong></td>
                <td><?php echo e($attendanceDate); ?></td>
              </tr>
              <tr>
                <td><strong>Day Name:</strong></td>
                <td><?php echo e(\Carbon\Carbon::parse($attendanceDate)->format('l')); ?></td>
              </tr>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <h4>Attendance Details</h4>
            <?php if($dayData): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th class="text-center">Status</th>
                    <th class="text-center">Check In</th>
                    <th class="text-center">Check Out</th>
                    <th class="text-center">Late Minutes</th>
                    <th class="text-center">Worked Minutes</th>
                    <th>System Remark</th>
                    <th>HR Remark</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-center">
                      <?php
                        $status = $dayData['status'] ?? '';
                      ?>
                      <?php if($status): ?>
                        <?php if($status == 'Present'): ?>
                          <span class="badge badge-success"><?php echo e($status); ?></span>
                        <?php elseif($status == 'Late'): ?>
                          <span class="badge badge-warning"><?php echo e($status); ?></span>
                        <?php elseif($status == 'Half Day'): ?>
                          <span class="badge badge-info"><?php echo e($status); ?></span>
                        <?php elseif($status == 'Absent'): ?>
                          <span class="badge badge-danger"><?php echo e($status); ?></span>
                        <?php elseif($status == 'Leave'): ?>
                          <span class="badge badge-primary"><?php echo e($status); ?></span>
                        <?php elseif($status == 'Holiday'): ?>
                          <span class="badge" style="background-color: #9b59b6;"><?php echo e($status); ?></span>
                        <?php elseif($status == 'Weekly Off'): ?>
                          <span class="badge badge-default"><?php echo e($status); ?></span>
                        <?php else: ?>
                          <span class="badge badge-default"><?php echo e($status); ?></span>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="badge badge-default">-</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($dayData['check_in'] ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($dayData['check_out'] ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($dayData['late_minutes'] ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($dayData['worked_minutes'] ?? '-'); ?></td>
                    <td><?php echo e($dayData['system_remark'] ?? '-'); ?></td>
                    <td><?php echo e($dayData['remarks'] ?? '-'); ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
              <strong>No Attendance Found</strong> for the selected date.
            </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-12">
            <table class="table table-bordered">
              <tr>
                <td><strong>Generated By:</strong> <?php echo e($generatedBy ?? 'System'); ?></td>
                <td><strong>Generated Date:</strong> <?php echo e($generatedDate ?? '-'); ?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/attendance_reports/employee_daily.blade.php ENDPATH**/ ?>