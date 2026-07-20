
<?php $__env->startSection('title', 'Roles List'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="page-header">Roles List <?php echo e(link_to_route('roles.create','Add Role',[],array('class'=>'btn btn-success pull-right'))); ?></h1>
<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>SI#</th>
            <th>Role Name</th>
            <th width="30%">Description</th> 
            <th class="center">Created</th>
            <th>Updated</th>
            <th>Status</th>
            <th>Action</th> 
          </tr>
        </thead>
        <tbody>
        <?php $options=''; $i=1; ?>
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
        <?php $name=addslashes($data->name); $options.='<option value="'.$data->id.'">'.$name.'</option>'; ?>
          <tr>
            <td><?php echo e($i); ?></td>
             <td><?php echo e($data->name); ?></td>
            <td><?php echo $data->description; ?></td>
            <td><?php echo e(showDateWithFormat($data->created_at)); ?></td>
            <td><?php echo e(showDateWithFormat($data->updated_at)); ?></td>
            <td><strong class="btn-<?php echo e(config('myhelpers.status_color')[$data->status]); ?>"><?php echo e(config('myhelpers.status')[$data->status]); ?></strong></td>
            <td> 
            	<?php echo HTML::decode(link_to_route('roles.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id))); ?>

              <?php if($data->is_deletable=='1'): ?>
              <button type="button" data-toggle="modal" data-target="#myModal" onClick="callModal('<?php echo e($data->id); ?>')" class='btn btn-danger btn-xs delete-button'>×</button>
              <?php endif; ?>
          </tr>
        <?php $i=$i+1; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Select any role for all the users under this role</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
      <?php echo e(Form::open(array('route' => array('roles.destroy', 'remove-id'),'method'=>'DELETE','class' =>'delete-form2', 'id'=>'del-form'))); ?>    
        <?php echo e(Form::select('role_id',array(),null,array('class' => 'form-control', 'id'=>'selectBox'))); ?> 
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/admin/roles/index.blade.php ENDPATH**/ ?>