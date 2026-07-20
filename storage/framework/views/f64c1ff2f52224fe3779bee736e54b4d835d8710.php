
<?php $__env->startSection('title', 'Edit Roles'); ?>
<?php $__env->startSection('content'); ?>
<?php $controllersArray=array_chunk($permissions,3,true); ?>

<h1 class="page-header">Edit Roles <?php echo e(link_to_route('roles.index','List All',null,array('class'=>'btn btn-success pull-right'))); ?></h1>   
<?php echo e(Form::model($roles,array('route' => array('roles.update', $roles->id),'class'=>'form-horizontal','method' => 'PUT'))); ?>  
<div class="row">		
	<div class="form-group">
		<label class="control-label col-sm-6">Role Name <sup>*</sup> :</label>
		<div class="col-md-6">
			<?php echo e(Form::text('name',null,array('class' => 'form-control', 'required' =>'required'))); ?>

			<?php echo e($errors->first('name', '<p class="text-danger">:message</p>' )); ?>

		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-6">Role Description : <sup>(maximum length should be 600)</sup></label>
		<div class="col-md-6">
			<?php echo e(Form::textarea('description',null,array('class' => 'form-control', 'rows'=>'3'))); ?>

		</div>
	</div>
	<?php if(($roles->is_deletable)==1): ?> 
	<div class="form-group">
		<div class="col-md-6">
			<label class="control-label col-sm-6">Status :</label>
			<div class="col-md-6">
				<?php echo e(Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))); ?>

			</div>
		</div>
	</div>
	<?php else: ?>
	<?php echo e(Form::hidden('status',1)); ?>

	<?php endif; ?>

</div>

<h3> Permission :</h3>
<?php $__currentLoopData = $controllersArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $elements): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="row">						
	<?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
	<div class="col-md-4">			
		<div class="checkbox controller">
			<label><input name="permissions[]" class="parent_<?php echo e($parent['name']); ?>" type="checkbox" <?php echo in_array($parent['id'], $checkPermissions)? "checked='true'":"" ;?> value="<?php echo e($parent['id']); ?>" onChange="permission_select_deselect_child(this)"> <strong><?php echo e($parent['name']); ?> </strong></label>
		</div>
		<div class="action-wraper">
			<?php $__currentLoopData = $parent['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
			<div class="checkbox actions" style="margin-left:20px;">
				<label><input name="permissions[]" class="<?php echo e($parent['name']); ?>" type="checkbox" <?php echo in_array($child['id'], $checkPermissions)? "checked='true'":"" ;?> value="<?php echo e($child['id']); ?>" onChange="permission_select_parent('<?php echo e($parent['name']); ?>')"> <?php echo e($child['name']); ?></label>
			</div>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</div>
	</div>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="form-group">
			<button type="submit" class="btn btn-success">Submit</button>
		</div>
	</div>
</div>
<?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?> 


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/roles/edit.blade.php ENDPATH**/ ?>