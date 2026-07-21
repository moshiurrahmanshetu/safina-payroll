<?php $__env->startSection('content'); ?>
<main class="main-body">
  <div class="login-body">
    <div class="login-inner-container">
      <div class="left-box">
        <div class="login-title-info">
          <h4 class="en">Park & Resort Management System (PRMS)</h4>
          <img src="<?php echo e(asset('public/img/logo.png')); ?>">

          <h1 class="en">Safina Park & Resort Ltd.</h1>
        </div>
      </div>
      <div class="right-box">
        <?php echo e(Form::open(array('route' => 'login','id'=>'sign_in'))); ?>

          <div class="form-title">
            <h1 class="en">Sign In</h1>
          </div>
          <div class="input-box">
            <img src="<?php echo e(asset('public/img/username-icon.png')); ?>">
            <?php echo e(Form::email('email',null,array('class' => 'input-item userid', 'placeholder' => 'Email Address','required'=>'required'))); ?>

          </div>
          <div class="input-box" style="position: relative;">
            <img src="<?php echo e(asset('public/img/password-icon.png')); ?>">
            <?php echo e(Form::password('password', array('class' => 'input-item userpass','id'=>'password', 'placeholder' => 'Password','required'=>'required'))); ?>

            <i class="fa fa-eye-slash" style="position: absolute; right: 10px; cursor: pointer;"></i>
            <i class="fa fa-eye hidden" style="position: absolute; right: 10px; cursor: pointer;"></i>
          </div>
          <div class="submit-box">
            <?php echo e(Form::submit('Sign In',array('class'=>'submit-btn','name'=>'sign_in'))); ?>

          </div>
          <div class="form-group"><br>
            <?php echo e(Form::checkbox('remember', null, false, array('class'=>'css-checkbox', 'id'=>'remember'))); ?>

            <?php echo e(Form::label('remember',  'Remember me', array('class'=>'css-label'))); ?>

            <a class="btn btn-link pull-right" style="padding-top: 0px; padding-right:0px" href="<?php echo e(route('password.request')); ?>">
              <?php echo e(__('Forgot Your Password?')); ?>

            </a>
          </div>
        <?php echo e(Form::close()); ?>

      </div>
    </div>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">
  $(".input-box i").click(function(){
    $(".input-box i").removeClass('hidden');
    $(this).addClass('hidden');
    var x = document.getElementById("password");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
  });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app-login', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\safina-payroll\resources\views/welcome.blade.php ENDPATH**/ ?>