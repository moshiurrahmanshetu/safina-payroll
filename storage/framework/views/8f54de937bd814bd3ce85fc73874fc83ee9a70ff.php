
<?php $__env->startSection('title', 'Create Roles'); ?>
<?php $__env->startSection('content'); ?>

<h1 class="page-header">Create Roles <?php echo e(link_to_route('roles.index','List All',null,array('class'=>'btn btn-success pull-right'))); ?></h1>
<?php echo e(Form::model(Request::old(),array('route' => array('roles.store'),'class'=>'form-horizontal'))); ?>  
<div class="row">		
	<div class="form-group">
		<label class="control-label col-sm-6">Role Name <sup>*</sup> :</label>
		<div class="col-md-6">
			<input type="text" name="name" class="form-control" required="required">
			<?php echo $errors->first('name', '<p class="text-danger">:message</p>' ); ?>

		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-6">Role Description : <sup>(maximum length should be 600)</sup></label>
		<div class="col-md-6">
			<textarea name="description" class="form-control" placeholder="maximum length should be 600"></textarea>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<label class="control-label col-sm-6">Status :</label>
			<div class="col-md-6">
				<?php echo e(Form::select('status',config('myhelpers.status'),null,array('class' => 'form-control'))); ?>

			</div>
		</div>

	</div>

	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				Create Role
			</button>
		</div>
	</div>
</div>

<?php $controllersArray=array_chunk($permission,3,true);?>

<h3> Permission : </h3>
<div><strong>Check/Uncheck All</strong> <input id="checkoruncheck" type="checkbox"> </div>
<?php $__currentLoopData = $controllersArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $elements): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="row">						
	<?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$elements): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
	<div class="col-md-4">			
		<div class="checkbox controller">
			<label><input name="<?php echo e($key); ?>" class="parent_<?php echo e($key); ?>" type="checkbox" checked="true" value="<?php echo e(array_search($key, $parents)); ?>" onChange="permission_select_deselect_child(this)"> <strong><?php echo e($key); ?> </strong></label>
		</div>
		<div class="action-wraper">
			<?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key2=>$element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<div class="checkbox actions" style="margin-left:20px;">
				<label><input name="<?php echo e($key2); ?>" class="<?php echo e($key); ?>" type="checkbox" checked="true" value="<?php echo e($key2); ?>" onChange="permission_select_parent('<?php echo e($key); ?>')"> <?php echo e($element); ?></label>
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

<?php $__env->startSection('script'); ?>
<script>
	$(document).ready(function(e){
		$("#checkoruncheck").change(function () {
			$("input:checkbox").prop('checked', $(this).prop("checked"));
		});
	});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/roles/create.blade.php ENDPATH**/ ?>