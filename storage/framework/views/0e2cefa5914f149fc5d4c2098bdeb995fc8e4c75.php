<?php $__env->startSection('title', 'Monthly Attendance Register'); ?>
<?php $__env->startSection('content'); ?>
<h3 class="page-header">Monthly Attendance Register <?php echo e(link_to_route('attendance_reports.index','Attendance Reports',[],array('class'=>'btn btn-success pull-right'))); ?></h3>

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
              <label>Month</label>
              <input type="month" class="form-control" id="report_month">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Department</label>
              <select class="form-control" id="department_id">
                <option value="">-- All Departments --</option>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($department->id); ?>"><?php echo e($department->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Shift</label>
              <select class="form-control" id="shift_id">
                <option value="">-- All Shifts --</option>
                <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($shift->id); ?>"><?php echo e($shift->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/attendance_reports/monthly_register.blade.php ENDPATH**/ ?>