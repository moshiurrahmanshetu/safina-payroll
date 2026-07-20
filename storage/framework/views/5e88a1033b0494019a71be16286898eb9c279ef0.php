<?php $__env->startSection('title', 'Employee Monthly Attendance Report'); ?>
<?php $__env->startSection('content'); ?>
<h3 class="page-header">Employee Monthly Attendance Report <?php echo e(link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))); ?></h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Filter Panel</h4>
      </div>
      <div class="panel-body">
        <form method="GET" action="<?php echo e(route('attendance_reports.employee_monthly')); ?>">
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
                <label>Attendance Month <span class="text-danger">*</span></label>
                <input type="month" class="form-control" name="attendance_month" value="<?php echo e(request('attendance_month')); ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>&nbsp;</label>
                <div>
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-file-text"></i> Generate Report
                  </button>
                  <?php if(isset($employee) && isset($attendanceMonth)): ?>
                  <a href="<?php echo e(route('attendance_reports.employee_monthly_print', ['employee_id' => $employee->id, 'attendance_month' => $attendanceMonth->attendance_month])); ?>" target="_blank" class="btn btn-default">
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

<?php if(isset($employee) && isset($attendanceMonth)): ?>
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
                <td><?php echo e($attendanceMonth->shift->name ?? 'N/A'); ?></td>
              </tr>
              <tr>
                <td><strong>Attendance Month:</strong></td>
                <td><?php echo e($attendanceMonth->attendance_month); ?></td>
              </tr>
              <tr>
                <td><strong>Locked Status:</strong></td>
                <td>
                  <?php if($attendanceMonth->attendance_locked): ?>
                    <span class="badge badge-danger">Locked</span>
                  <?php else: ?>
                    <span class="badge badge-success">Unlocked</span>
                  <?php endif; ?>
                </td>
              </tr>
            </table>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-md-12">
            <h4>Summary</h4>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Present</th>
                    <th class="text-center">Late</th>
                    <th class="text-center">Half Day</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Leave</th>
                    <th class="text-center">Holiday</th>
                    <th class="text-center">Weekly Off</th>
                    <th class="text-center">Total Holidays</th>
                    <th class="text-center">Total Weekly Off</th>
                    <th class="text-center">Expected Working Days</th>
                    <th class="text-center">Attendance %</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-center"><span class="badge badge-success"><?php echo e($attendanceMonth->summary_present ?? 0); ?></span></td>
                    <td class="text-center"><span class="badge badge-warning"><?php echo e($attendanceMonth->summary_late ?? 0); ?></span></td>
                    <td class="text-center"><span class="badge badge-info"><?php echo e($attendanceMonth->summary_halfday ?? 0); ?></span></td>
                    <td class="text-center"><span class="badge badge-danger"><?php echo e($attendanceMonth->summary_absent ?? 0); ?></span></td>
                    <td class="text-center"><span class="badge badge-primary"><?php echo e($attendanceMonth->summary_leave ?? 0); ?></span></td>
                    <td class="text-center"><span class="badge" style="background-color: #9b59b6;"><?php echo e($attendanceMonth->summary_holiday ?? 0); ?></span></td>
                    <td class="text-center"><span class="badge badge-default"><?php echo e($attendanceMonth->summary_weekly_off ?? 0); ?></span></td>
                    <td class="text-center"><strong><?php echo e($totalHolidays ?? 0); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($totalWeeklyOff ?? 0); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($expectedWorkingDays ?? 0); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($attendancePercentage ?? 0); ?>%</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <h4>Attendance Details</h4>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th class="text-center">Date</th>
                    <th class="text-center">Day</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Check In</th>
                    <th class="text-center">Check Out</th>
                    <th class="text-center">Late Minutes</th>
                    <th class="text-center">Worked Minutes</th>
                    <th class="text-center">System Remark</th>
                    <th class="text-center">HR Remark</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                     $daysInMonth = \Carbon\Carbon::parse($attendanceMonth->attendance_month . '-01')->daysInMonth;
                  ?>
                  <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                    <?php
                      $date = \Carbon\Carbon::parse($attendanceMonth->attendance_month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT));
                      $dateKey = $date->format('Y-m-d');
                      $dayData = $attendanceJson[$dateKey] ?? [];
                      $status = $dayData['status'] ?? '';
                    ?>
                    <tr>
                      <td class="text-center"><?php echo e($date->format('Y-m-d')); ?></td>
                      <td class="text-center"><?php echo e($date->format('l')); ?></td>
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
                  <?php endfor; ?>
                </tbody>
              </table>
            </div>
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

        <div class="row mt-4">
          <div class="col-md-12">
            <table class="table table-bordered">
              <tr>
                <td style="width: 33%;">
                  <strong>Prepared By:</strong>
                  <br><br><br>
                  __________________
                  <br><br>
                </td>
                <td style="width: 33%;">
                  <strong>Checked By:</strong>
                  <br><br><br>
                  __________________
                  <br><br>
                </td>
                <td style="width: 34%;">
                  <strong>Approved By:</strong>
                  <br><br><br>
                  __________________
                  <br><br>
                </td>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/attendance_reports/employee_monthly.blade.php ENDPATH**/ ?>