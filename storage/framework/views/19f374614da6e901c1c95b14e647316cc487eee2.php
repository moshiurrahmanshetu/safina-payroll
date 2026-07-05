<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Sahadat">
  <!-- CSRF Token -->
  <link rel="icon" type="image/ico" href="<?php echo e(asset('public/img/logo.png')); ?>" sizes="any" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo e(config('app.name', 'eStore Management | Safina Park & Resort, Godagari, Rajshahi, Bangladesh')); ?></title>
  <!-- Scripts -->
  <script src="<?php echo e(asset('public/js/app.js')); ?>" defer></script>
  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
  <!-- Styles -->
  <link href="<?php echo e(asset('public/css/font-awesome.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('public/css/app.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('public/css/login.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('public/css/login-custom.css')); ?>" rel="stylesheet">
</head>
<body>

    <!--for validation error start-->
    <div class="flash-message">
      <?php $__currentLoopData = ['danger', 'warning', 'success', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if(Session::has('flash_' . $msg)): ?>
      <p class="alert alert-<?php echo e($msg); ?>"><a href="#" class="close" data-dismiss="alert">&times;</a> <strong> <?php echo e($msg); ?>!!</strong> <?php echo e(Session::get('flash_' . $msg)); ?></p>
      <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <!--for validation error end-->

      <?php echo $__env->yieldContent('content'); ?>


  <footer class="app-footer">
    <div class="col-sm-6">
      <a href="">Copyright © 2025</a>
      <span>All Rights Reserved. Safina Park & Resort, Godagari, Rajshahi, Bangladesh</span>
    </div>
    <div class="col-sm-6 ml-auto">
      <span>Developed by</span>
      <a href="https://atomsoft.com.bd" target="_blank">AtomSoft</a>
    </div>
  </footer>
  <script src="<?php echo e(asset('public/js/jquery-1.10.2.js')); ?>"></script>
  <!-- only custom script section start -->
    <?php echo $__env->yieldContent('script'); ?>
  <!-- only custom script section start --> 
</body>
</html><?php /**PATH C:\xampp\htdocs\safina-live\resources\views/layouts/app-login.blade.php ENDPATH**/ ?>