<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Open Source Bootstrap Admin Template">
    <meta name="author" content="Sahadat">
    <meta name="keyword" content="Atom Soft for Software Development">
    <title><?php echo $__env->yieldContent('title','eStore Management System, Safina Park & Resort, Godagari, Rajshahi, Bangladesh'); ?></title>
    <!-- Icons-->
    <link rel="icon" type="image/ico" href="<?php echo e(asset('public/img/favicon.ico')); ?>" sizes="any" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <link href="<?php echo e(asset('public/css/coreui-icons.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('public/css/flag-icon.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('public/css/font-awesome.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('public/css/simple-line-icons.css')); ?>" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="<?php echo e(asset('public/css/style.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('public/css/pace.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('public/css/jquery-ui.css')); ?>" rel ="stylesheet">
    <!-- only custom css section start -->
    <?php echo $__env->yieldContent('css'); ?>
    <link href="<?php echo e(asset('public/css/custom.css')); ?>" rel="stylesheet">
    <style type="text/css">
      @media  print {
        .no-print, .app-footer, .alert{
          display: none;
        }
      }
    </style>
    <!-- only custom css section end -->
    <!-- Scripts -->
    <script>
      window.Laravel = <?php echo json_encode([
          'csrfToken' => csrf_token(),
      ]); ?>
    </script>
  </head>
  <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
    <!-- header section start -->
    <?php echo $__env->make('admin.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- header section end -->
    <div class="app-body">
      <div class="sidebar">
        <!-- nav section start -->
         <?php echo $__env->make('admin.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- nav section end -->
        <button class="sidebar-minimizer brand-minimizer" type="button"></button>
      </div>
      <main class="main">
        <!-- Breadcrumb section start -->
        <br>
        <!-- Breadcrumb section end -->
        <div class="container-fluid">
          <!--for validation error start-->
          <div class="flash-message">
            <?php $__currentLoopData = ['danger', 'warning', 'success', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if(Session::has('flash_' . $msg)): ?>
              <p class="alert alert-<?php echo e($msg); ?>"><a href="#" class="close" data-dismiss="alert">&times;</a> <strong> <?php echo e($msg); ?>!!</strong> <?php echo e(Session::get('flash_' . $msg)); ?></p>
              <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <!--for validation error end-->
          <!-- main content section start -->
            <?php echo $__env->yieldContent('content','No Content Found'); ?>
          <!-- main content section start --> 
        </div>
      </main>
      <!-- Breadcrumb section start -->
       <?php echo $__env->make('admin.aside', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <!-- Breadcrumb section end -->
    </div>    
    <!-- footer section start -->
     <?php echo $__env->make('admin.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- footer section end -->
    <!-- CoreUI and necessary plugins-->
    <script src="<?php echo e(asset('public/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('public/js/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('public/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('public/js/pace.min.js')); ?>"></script>
    <script src="<?php echo e(asset('public/js/perfect-scrollbar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('public/js/coreui.min.js')); ?>"></script>
    <!-- Plugins and scripts required by this view-->
    <script src="<?php echo e(asset('public/js/jquery-1.10.2.js')); ?>"></script>
    <script src="<?php echo e(asset('public/js/jquery-ui.js')); ?>"></script>
    <script src="<?php echo e(asset('public/js/common.js')); ?>"></script>
    <?php echo $__env->make('ajaxs.php_script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- only custom "script with PHP Page"  section start -->
    <script>
      $(function() {
        $( ".datetimepicker1" ).datepicker();
      });
    /* ............for delete button start...................*/
    $(document).on('click', '.delete-form', function() {
      return confirm('Are you sure? you want to Delete?');
    });
    </script>
    <!-- only custom script section start -->
      <?php echo $__env->yieldContent('script'); ?>
    <!-- only custom script section start --> 
  </body>
</html><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/layouts/admin.blade.php ENDPATH**/ ?>