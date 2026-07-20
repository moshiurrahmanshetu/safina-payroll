<?php $__env->startSection('title', 'Attendance Month Details'); ?>
<?php $__env->startSection('content'); ?>
<h3 class="page-header">Attendance Month Details <?php echo e(link_to_route('attendances.index','Attendance List',[],array('class'=>'btn btn-success pull-right'))); ?></h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4>Employee Information</h4>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Employee:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->user ? $attendanceMonth->user->name : 'N/A'); ?></strong></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Month:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->attendance_month); ?></strong></p>
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
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Present:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->summary_present); ?></strong></p>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Late:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->summary_late); ?></strong></p>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Half Day:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->summary_halfday); ?></strong></p>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Absent:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->summary_absent); ?></strong></p>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Leave:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->summary_leave); ?></strong></p>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Holiday:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->summary_holiday); ?></strong></p>
            </div>
          </div>
        </div>
        <div class="row" style="margin-top: 10px;">
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Weekly Off:</label>
              <p class="form-control-static"><strong><?php echo e($attendanceMonth->summary_weekly_off); ?></strong></p>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Locked:</label>
              <p class="form-control-static">
                <?php if($attendanceMonth->attendance_locked): ?>
                  <span class="badge badge-danger">Locked</span>
                <?php else: ?>
                  <span class="badge badge-success">Unlocked</span>
                <?php endif; ?>
              </p>
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
        <h4>Daily Attendance</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Day</th>
                <th>Status</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $attendanceJson = $attendanceMonth->attendance_json ?? [];
                $daysInMonth = date('t', strtotime($attendanceMonth->attendance_month . '-01'));
              ?>
              <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                <?php
                  $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
                  $dayData = $attendanceJson[$dayKey] ?? null;
                ?>
                <tr>
                  <td><?php echo e($dayKey); ?></td>
                  <td>
                    <?php if($dayData): ?>
                      <strong><?php echo e($dayData['status'] ?? '-'); ?></strong>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($dayData['check_in'] ?? '-'); ?></td>
                  <td><?php echo e($dayData['check_out'] ?? '-'); ?></td>
                  <td><?php echo e($dayData['remarks'] ?? '-'); ?></td>
                </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

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
              <p class="form-control-static"><?php echo e($attendanceMonth->creator ? $attendanceMonth->creator->name : 'N/A'); ?></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Created At:</label>
              <p class="form-control-static"><?php echo e($attendanceMonth->created_at ? $attendanceMonth->created_at->format('Y-m-d H:i:s') : 'N/A'); ?></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Updated By:</label>
              <p class="form-control-static"><?php echo e($attendanceMonth->updater ? $attendanceMonth->updater->name : 'N/A'); ?></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Updated At:</label>
              <p class="form-control-static"><?php echo e($attendanceMonth->updated_at ? $attendanceMonth->updated_at->format('Y-m-d H:i:s') : 'N/A'); ?></p>
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
      <?php if(!$attendanceMonth->attendance_locked): ?>
        <?php echo HTML::decode(link_to_route('attendances.edit', '<i class="nav-icon icon-pencil"></i> Edit', array($attendanceMonth->id), array('class' => 'btn btn-primary'))); ?>

      <?php endif; ?>
      <?php echo HTML::decode(link_to_route('attendances.index', '<i class="nav-icon icon-arrow-left"></i> Back', [], array('class' => 'btn btn-default'))); ?>

    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/attendances/show.blade.php ENDPATH**/ ?>