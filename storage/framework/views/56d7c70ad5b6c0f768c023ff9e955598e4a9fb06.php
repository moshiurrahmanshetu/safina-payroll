<?php $__env->startSection('title', 'Permanent Employee List'); ?>
<?php $__env->startSection('content'); ?>
<h3 class="page-header">Permanent Employee List <?php if($permanent_employees): ?> (<?php echo e(count($permanent_employees)); ?>) <?php endif; ?> <?php echo e(link_to_route('permanent_employees.create','Add Permanent Employee',[],array('class'=>'btn btn-success pull-right'))); ?></h3>

<!-- Filter Form -->
<div class="row">
  <div class="col-md-12">
    <?php echo e(Form::open(array('route' => 'permanent_employees.index', 'method'=>'GET', 'class'=>'form-horizontal'))); ?>

    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Search:</label>
          <input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Name, Employee ID, Mobile">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Department:</label>
          <select name="department_id" class="form-control">
            <option value="">All Departments</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($id); ?>" <?php echo e(request('department_id') == $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Designation:</label>
          <select name="designation_id" class="form-control">
            <option value="">All Designations</option>
            <?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($id); ?>" <?php echo e(request('designation_id') == $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Status:</label>
          <select name="employment_status" class="form-control">
            <option value="">All Status</option>
            <option value="1" <?php echo e(request('employment_status') == '1' ? 'selected' : ''); ?>>Active</option>
            <option value="0" <?php echo e(request('employment_status') == '0' ? 'selected' : ''); ?>>Inactive</option>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>&nbsp;</label>
          <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?php echo e(route('permanent_employees.index')); ?>" class="btn btn-danger">Reset</a>
          </div>
        </div>
      </div>
    </div>
    <?php echo e(Form::close()); ?>

  </div>
</div>
<br>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Employee ID</th>
            <th>Photo</th>
            <th>Full Name</th>
            <th>Mobile</th>
            <th>Department</th>
            <th>Designation</th>
            <th>Joining Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
         <?php $i=1; ?>
         <?php $__currentLoopData = $permanent_employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <tr>
          <td><?php echo e($i); ?></td>
          <td><strong><?php echo e($data->employee_id); ?></strong></td>
          <td>
            <?php if($data->photo): ?>
              <img src="<?php echo e(asset($data->photo)); ?>" alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
              <span class="text-muted">No Photo</span>
            <?php endif; ?>
          </td>
          <td><?php echo e($data->full_name); ?></td>
          <td><?php echo e($data->mobile); ?></td>
          <td><?php echo e($data->department ? $data->department->name : 'N/A'); ?></td>
          <td><?php echo e($data->designation ? $data->designation->name : 'N/A'); ?></td>
          <td><?php echo e(date('d-m-Y',strtotime($data->joining_date))); ?></td>
          <td><strong class="btn-<?php echo e(config('myhelpers.status_color')[$data->employment_status]); ?>"><?php echo e(config('myhelpers.status')[$data->employment_status]); ?></strong></td>
          <td>
           <?php echo HTML::decode(link_to_route('permanent_employees.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id))); ?>

           <?php echo e(Form::open(array('route' => array('permanent_employees.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form'))); ?>

           <button type="submit" class="btn btn-danger delete-form" ><i class="nav-icon icon-trash"></i></button>
           <?php echo e(Form::close()); ?>

         </td>
       </tr>
       <?php $i=$i+1; ?>
       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     </tbody>
   </table>
 </div>

</div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/permanent_employees/index.blade.php ENDPATH**/ ?>