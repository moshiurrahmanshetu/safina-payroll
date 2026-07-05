<?php $__env->startSection('content'); ?>
<div class="row">
  <?php if(session('status')): ?>
  <div class="alert alert-success" role="alert">
    <?php echo e(session('status')); ?>

  </div>
  <?php endif; ?>
  <h1 class="overview-heading">Overview of My Dashboard</h1>
</div>

<div class="animated fadeIn">
  <div class="row">
    <!-- /.col-->
    <?php if(checkMenuActive(['RegisterController@showUserLists'],$menu_list)): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <?php $total=0; ?>
          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $total+=$data->total; ?>
            <div class="text-value"><?php echo e($data->total); ?> </div>
            <div>Number of <?php if($data->status==0): ?> Inactive <?php else: ?> Active <?php endif; ?> Users</div>
            <hr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <div class="text-value"># <?php echo e($total); ?></div>
          <div>Number of Total Users</div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php if(checkMenuActive(['DesignationController@create','DepartmentController@create'],$menu_list)): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white bg-info">
        <div class="card-body">
          <div class="text-value"># <?php echo e($designations); ?></div>
          <div>Number of Designations</div>
          <hr>
          <div class="text-value"># <?php echo e($departments); ?> </div>
          <div>Number of Departments</div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php if(checkMenuActive(['CategoryController@create','ItemController@index'],$menu_list)): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white" style="background-color:#6a0dad;">
        <div class="card-body">
          <div class="text-value"># <?php echo e($categories); ?></div>
          <div>Number of Categories</div>
          <hr>
          <div class="text-value"># <?php echo e($products); ?></div>
          <div>Number of Products</div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <!-- /.col-->
    <?php if(checkMenuActive(['SupplierController@index','PurchaseController@index'],$menu_list)): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white" style="background-color:#0c4da2;">
        <div class="card-body">
          <div class="text-value"># <?php echo e($suppliers); ?></div>
          <div>Number of Suppliers</div>
          <hr>
          <div class="text-value"># <?php echo e($purchases); ?> </div>
          <div>Number of Purchase</div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <!-- /.col-->
    <?php if(checkMenuActive(['RequisitionController@index','IndentController@indent_list'],$menu_list)): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white" style="background-color:#139381;">
        <div class="card-body">
          <div class="text-value"># <?php echo e($myrequisitions); ?></div>
          <div>Number of My Requisitions</div>
          <hr>
          <div class="text-value"># <?php echo e($my_indent); ?></div>
          <div>Number of My Indents</div>
          <hr>
          <div class="text-value"># <?php echo e($my_mrs_items); ?></div>
          <div>Number of My MRS Items</div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <!-- /.col-->
    <?php if(checkMenuActive(['RequisitionController@admin_requisition_list','IndentController@admin_indent_list'],$menu_list)): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <div class="text-value"># <?php echo e($requisitions); ?></div>
          <div>Number of Total Requisitions</div>
          <hr>
          <div class="text-value"># <?php echo e($all_indent); ?></div>
          <div>Number of Total Indents</div>
          <hr>
          <div class="text-value"># <?php echo e($mrs_items); ?></div>
          <div>Number of Total MRS Items</div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <!-- /.col-->
    <?php if(checkMenuActive(['StockInController@low_stock_reminder'],$menu_list)): ?>
    <div class="col-sm-12 col-md-6"><h3>Low Stock Reminder <a class="pull-right" href="<?php echo e(route('low_stock_reminder')); ?>">View All</a></h3>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <td>#</td>
              <th>Product Name</th>
              <th>Balance</th>
              <th class="text-center">Low Stock Value</th>
            </tr>
          </thead>
          <tbody id="low_stock_load">

          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </div>
  <!-- /.row-->

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(asset('public/js/custom-tooltips.min.js')); ?>"></script>
<script type="text/javascript">
$(document).ready(function(){
  var req_item_id='';
  $.ajax({
    type: "get",
    url:"<?php echo e(route('ajax.lowstock_summary')); ?>",
    data:{req_item_id:req_item_id},
    success: function(data){
      if(data){
        $.each(data, function(key, value) {
          var html='';
          html='<tr><td>'+key+'</td><td>'+value.name+'</td><td>'+value.balance+'</td><td>'+value.low_stock+'</td></tr>';
          $('#low_stock_load').append($(html));
        });
      }
    }
  });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-live\resources\views/home.blade.php ENDPATH**/ ?>