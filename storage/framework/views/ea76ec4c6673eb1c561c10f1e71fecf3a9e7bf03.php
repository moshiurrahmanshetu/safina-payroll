<?php $__env->startSection('title', 'Employee Daily Attendance Report - Print'); ?>

<?php $__env->startSection('report_title', 'Employee Daily Attendance Report'); ?>
<?php $__env->startSection('report_subtitle', 'Daily Attendance Details'); ?>

<?php $__env->startSection('content'); ?>
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
        <table class="table table-bordered">
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
                <tr style="page-break-inside: avoid;">
                    <td class="text-center">
                        <?php
                            $status = $dayData['status'] ?? '';
                        ?>
                        <?php if($status): ?>
                            <?php if($status == 'Present'): ?>
                                <span style="color: green; font-weight: bold;"><?php echo e($status); ?></span>
                            <?php elseif($status == 'Late'): ?>
                                <span style="color: orange; font-weight: bold;"><?php echo e($status); ?></span>
                            <?php elseif($status == 'Half Day'): ?>
                                <span style="color: #17a2b8; font-weight: bold;"><?php echo e($status); ?></span>
                            <?php elseif($status == 'Absent'): ?>
                                <span style="color: red; font-weight: bold;"><?php echo e($status); ?></span>
                            <?php elseif($status == 'Leave'): ?>
                                <span style="color: #007bff; font-weight: bold;"><?php echo e($status); ?></span>
                            <?php elseif($status == 'Holiday'): ?>
                                <span style="color: #9b59b6; font-weight: bold;"><?php echo e($status); ?></span>
                            <?php elseif($status == 'Weekly Off'): ?>
                                <span style="color: gray; font-weight: bold;"><?php echo e($status); ?></span>
                            <?php else: ?>
                                <span style="color: gray;"><?php echo e($status); ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: gray;">-</span>
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
        <?php else: ?>
        <div class="alert alert-warning" style="text-align: center;">
            <strong>No Attendance Found</strong> for the selected date.
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-4" style="page-break-inside: avoid;">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <td><strong>Generated By:</strong> <?php echo e($generatedBy ?? 'System'); ?></td>
                <td><strong>Generated Date:</strong> <?php echo e($generatedDate ?? '-'); ?></td>
                <td><strong>Printed Date:</strong> <?php echo e($printedDate ?? '-'); ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-4" style="page-break-inside: avoid;">
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

<script>
    window.onload = function() {
        window.print();
    };
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.attendance_reports.print.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/attendance_reports/employee_daily_print.blade.php ENDPATH**/ ?>