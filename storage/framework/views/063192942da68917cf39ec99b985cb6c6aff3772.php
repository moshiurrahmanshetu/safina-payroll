
<?php $__env->startSection('title', 'User Lists'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="page-header">User Lists <?php echo e(link_to_route('users.create','Add User',[],array('class'=>'btn btn-success pull-right'))); ?></h1> 
<?php echo e(session()->get('langsname')); ?>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>SI#</th>
            <th>Name</th>
            <th>Photo</th>
            <th>Designation</th>
            <th>Department</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $i=1; ?>
          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
          <tr>
            <td><?php echo e($i); ?></td>
            <td><?php echo e($data->name); ?></td>
            <td><?php echo e(HTML::image('storage/app/admin/users/'.$data->photo, null, array('width'=>'70', 'class'=>'img-responsive'))); ?></td>
            <td><?php echo e($data->designation->name); ?></td>
            <td><?php echo e($data->department->name); ?></td>
            <td><?php echo e($data->email); ?></td>
            <td><?php echo e($data->mobile_no); ?></td>
            <td><?php echo e($data->role->name); ?></td>
            
            <td><strong class="btn-<?php echo e(config('myhelpers.status_color')[$data->status]); ?>"><?php echo e(config('myhelpers.status')[$data->status]); ?></strong></td>
            <td> 
              <?php echo HTML::decode(link_to_route('users.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id))); ?>

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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-live\resources\views/auth/show_user_lists.blade.php ENDPATH**/ ?>