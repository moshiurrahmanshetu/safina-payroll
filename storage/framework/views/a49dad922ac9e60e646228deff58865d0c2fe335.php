
<?php $__env->startSection('title', 'Purchase List'); ?>
<?php $__env->startSection('content'); ?>
<div class="row page-header">
  <div class="col-sm-12 col-md-3"><h3>Purchase List </h3></div>
  <div class="col-sm-12 col-md-7"><h3>
    <form action="" method="GET" role="search" >
      <table class="table table-borderless">
        <tr>
          <td><?php echo e(Form::select('supplier_id',array(''=>'Select Supplier Com.')+$supllier_lists,$search_array['supplier_id'],array('class' => 'form-control'))); ?></td> 
          <?php if($search_array['start_date']){ $s_date=date('d-m-Y',strtotime($search_array['start_date'])); }else{ $s_date=''; }
          if($search_array['end_date']){ $e_date=date('d-m-Y',strtotime($search_array['end_date'])); }else{ $e_date=''; } ?>
          <td> <?php echo e(Form::text('start_date',$s_date,array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'Start Date'))); ?> </td> 
          <td> <?php echo e(Form::text('end_date',$e_date,array('class' => 'form-control datetimepicker1', 'autocomplete'=>'off', 'placeholder'=> 'End Date'))); ?> </td>
          <td><span class="input-group-btn">
            <button type="submit" class="btn btn-default search_btn" name="BTSubmit">
              <span class="fa fa-search"></span>
            </button>
          </span>
        </td>
        <?php if($search_array['start_date']!=''): ?>
        <td> 
          <!-- <a class="btn btn-info pull-right txt_white" target="_blank" href="<?php echo e(route('purchase_print',['download'=>'purchase','start_date'=>$search_array['start_date'],'end_date'=>$search_array['end_date']])); ?>">Print</a> -->
        </td>
        <?php endif; ?>
      </tr>
    </table> 
  </form> </h3>
</div>
<div class="col-sm-12 col-md-2"><h1>
  <?php echo e(link_to_route('purchase.create','New Purchase',[],array('class'=>'btn btn-success pull-right'))); ?> </h1>
</div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr> 
            <td>#</td>
            <th>Supplier Name(Company)</th>
            <th>Mobile</th>
            <th>Purchase Date</th>
            <th>Purch. Items (Qty.)</th>
            <th>Grand Total TK</th>
            <th>Due / Paid TK</th>
            <!-- <th>PO Number</th> -->
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $i=1; ;?>  
          <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($i); ?></td>
            <td><?php echo $data->supplier->contact_name; ?>(<?php echo $data->supplier->company_name; ?>)</td>
            <td><?php echo $data->supplier->mobile; ?></td>
            <td><?php echo e(date('d-m-Y',strtotime($data->purchase_date))); ?></td> 
            <td>
              <?php $__currentLoopData = $data->purchase_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $items->name; ?> (<?php echo $items->quantity+0; ?>)<br> 
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td><?php echo $data->grand_total+0; ?></td>
            <td>
              <?php if($data->purchase_transactions): ?>
                <?php $__currentLoopData = $data->purchase_transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php $due=$data->grand_total-$item->amount; ?>
                  <?php if($due == (int)$due): ?>
                    <?php echo e($due); ?>

                  <?php else: ?>
                    <?php echo e(number_format($due,2)); ?>

                  <?php endif; ?>
                  / <?php echo e($item->amount+0); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php else: ?>
                <?php echo $data->grand_total+0; ?>

              <?php endif; ?>
            </td>
            <!-- <td><?php echo $data->po_number; ?></td> -->
            <td><strong class="btn-<?php echo e(config('myhelpers.purchase_status_color')[$data->status]); ?>"><?php echo e(config('myhelpers.purchase_status')[$data->status]); ?></strong></td>
            <td>
              <?php echo e(link_to_route('purchase_transaction.create','+ TK ',['id'=>$data->id],array('class'=>'btn btn-primary pull-center'))); ?>

              <?php echo HTML::decode(link_to_route('purchase.edit', '<i class="nav-icon icon-pencil"></i>', array($data->id))); ?>

              <?php echo e(Form::open(array('route' => array('purchase.destroy', $data->id), 'method'=>'DELETE', 'id'=>'del-form'))); ?>

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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-live\resources\views/admin/purchase/index.blade.php ENDPATH**/ ?>