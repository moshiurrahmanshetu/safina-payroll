<?php $__env->startSection('title', 'Daily Attendance Register'); ?>
<?php $__env->startSection('content'); ?>
<h3 class="page-header">Daily Attendance Register <?php echo e(link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))); ?></h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <form method="GET" action="<?php echo e(route('attendance_reports.daily_register')); ?>">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Attendance Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="attendance_date" value="<?php echo e(request('attendance_date')); ?>" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Department (Optional)</label>
                <select class="form-control" name="department_id">
                  <option value="">-- All Departments --</option>
                  <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($department->id); ?>" <?php echo e(request('department_id') == $department->id ? 'selected' : ''); ?>>
                      <?php echo e($department->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Shift (Optional)</label>
                <select class="form-control" name="shift_id">
                  <option value="">-- All Shifts --</option>
                  <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($shift->id); ?>" <?php echo e(request('shift_id') == $shift->id ? 'selected' : ''); ?>>
                      <?php echo e($shift->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Status (Optional)</label>
                <select class="form-control" name="status">
                  <option value="">-- All Status --</option>
                  <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>>
                      <?php echo e($value); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-file-text"></i> Generate Report
                </button>
                <?php if(isset($attendanceDate)): ?>
                <a href="<?php echo e(route('attendance_reports.daily_register_print', ['attendance_date' => $attendanceDate, 'department_id' => request('department_id'), 'shift_id' => request('shift_id'), 'status' => request('status')])); ?>" target="_blank" class="btn btn-default">
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
        </form>
      </div>
    </div>
  </div>
</div>

<?php if(isset($attendanceDate)): ?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Report Output</h4>
      </div>
      <div class="panel-body">
        <div class="text-center mb-4">
          <h2><?php echo e($companyName); ?></h2>
          <?php if($companyAddress): ?>
          <p><?php echo e($companyAddress); ?></p>
          <?php endif; ?>
          <h3><?php echo e($reportTitle); ?></h3>
          <p><strong>Attendance Date:</strong> <?php echo e($attendanceDate); ?></p>
          <?php if(request('department_id')): ?>
          <?php $selectedDept = $departments->firstWhere('id', request('department_id')); ?>
          <p><strong>Department:</strong> <?php echo e($selectedDept ? $selectedDept->name : 'All Departments'); ?></p>
          <?php endif; ?>
          <?php if(request('shift_id')): ?>
          <?php $selectedShift = $shifts->firstWhere('id', request('shift_id')); ?>
          <p><strong>Shift:</strong> <?php echo e($selectedShift ? $selectedShift->name : 'All Shifts'); ?></p>
          <?php endif; ?>
        </div>

        <div class="row">
          <div class="col-md-12">
            <?php if(count($attendanceData) > 0): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px;">SL</th>
                    <th class="text-center">Employee ID</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Assigned Shift</th>
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
                  <?php $__currentLoopData = $attendanceData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $employee = $data['employee'];
                    $dayData = $data['dayData'];
                    $status = $dayData['status'] ?? '';
                  ?>
                  <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td class="text-center"><?php echo e($employee->employee_id ?? $employee->id); ?></td>
                    <td><?php echo e($employee->name); ?></td>
                    <td><?php echo e($employee->department->name ?? 'N/A'); ?></td>
                    <td><?php echo e($employee->designation->name ?? 'N/A'); ?></td>
                    <td><?php echo e($employee->shift->name ?? 'N/A'); ?></td>
                    <td class="text-center">
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
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
              <strong>No Attendance Found</strong> for the selected criteria.
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/attendance_reports/daily_register.blade.php ENDPATH**/ ?>