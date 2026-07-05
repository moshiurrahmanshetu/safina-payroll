
<?php $__env->startSection('title', 'Create Designation'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="page-header">Create Designation </h1>
<?php echo e(Form::model(Request::old(),array('route' => array('designation.store'),'enctype'=>'multipart/form-data','class'=>'form-horizontal'))); ?>

<div class="row">

	<div class="form-group">
		<label class="control-label col-sm-2">Designation Name <sup>*</sup> :</label>
		<div class="col-md-6">
			<?php echo e(Form::text('name',null,array('class' => 'form-control', 'required'=>'required'))); ?>

			<?php echo $errors->first('name', '<p class="text-danger">:message</p>' ); ?>

		</div>
	</div>

	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				Create Designation
			</button>
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-8 col-md-offset-1">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>SL NO</th>
						<th>Name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php $id= 1; $designation = '';?>
					<?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php $name=addslashes($data->name);
					$designation.='<option value="'.$data->id.'">'.$name.'</option>';
					?>
					<tr>
						<td><?php echo e($id); ?></td>
						<td><?php echo e($data->name); ?></td>
						<td>
							<button type="button" data-toggle="modal" data-target="#deleteModal" onClick="deleteDesignation('<?php echo e($data->id); ?>')" class='btn btn-danger btn-xs delete-button'>×</button>
						</td>
					</tr>
					<?php $id++; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php echo e(Form::close()); ?>


<!-- Delete designation modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Select any Designation for all the users under this Designation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<?php echo e(Form::open(array('route' => array('designation.destroy', 'remove-id'),'method'=>'DELETE',
				'class' =>'delete-form2', 'id'=>'designationDelete'))); ?>  
				<?php echo e(Form::select('designation_id',array(),null,array('class' => 'form-control', 'id'=>'selectDesBox'))); ?> 
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<?php echo e(Form::submit('Confirm Delete',array('class'=>'btn btn-primary delete-form'))); ?>

			</div>
			<?php echo e(Form::close()); ?>

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script> 
	function deleteDesignation(selector){
		var designation='<?php if(isset($designation)){ echo $designation; } ?>'; 
		var my_obj = $('#designationDelete');
		var my_action = my_obj.attr('action');
		var my_id = selector;

		var urlaction = my_action.substring(0, my_action.lastIndexOf("/") + 1);
		var my_actions = urlaction+my_id;

		my_obj.attr('action', my_actions);
		$('#selectDesBox').empty();
		$("#selectDesBox").append(designation);
		$("#selectDesBox option[value='"+my_id+"']").remove();
	}
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-live\resources\views/admin/designations/create.blade.php ENDPATH**/ ?>