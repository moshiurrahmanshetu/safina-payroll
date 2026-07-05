@extends('layouts.app-login')
@section('content')
<main class="main-body">
  <div class="login-body">
    <div class="login-inner-container">
      <div class="left-box">
        <div class="login-title-info">
          <h4 class="en">Park & Resort Management System (PRMS)</h4>
          <img src="{{ asset('public/img/logo.png') }}">

          <h1 class="en">Safina Park & Resort Ltd.</h1>
        </div>
      </div>
      <div class="right-box">
        {{ Form::open(array('route' => 'login','id'=>'sign_in')) }}
          <div class="form-title">
            <h1 class="en">Sign In</h1>
          </div>
          <div class="input-box">
            <img src="{{ asset('public/img/username-icon.png') }}">
            {{Form::email('email',null,array('class' => 'input-item userid', 'placeholder' => 'Email Address','required'=>'required'))}}
          </div>
          <div class="input-box" style="position: relative;">
            <img src="{{ asset('public/img/password-icon.png') }}">
            {{Form::password('password', array('class' => 'input-item userpass','id'=>'password', 'placeholder' => 'Password','required'=>'required'))}}
            <i class="fa fa-eye-slash" style="position: absolute; right: 10px; cursor: pointer;"></i>
            <i class="fa fa-eye hidden" style="position: absolute; right: 10px; cursor: pointer;"></i>
          </div>
          <div class="submit-box">
            {{ Form::submit('Sign In',array('class'=>'submit-btn','name'=>'sign_in'))}}
          </div>
          <div class="form-group"><br>
            {{ Form::checkbox('remember', null, false, array('class'=>'css-checkbox', 'id'=>'remember'))}}
            {{ Form::label('remember',  'Remember me', array('class'=>'css-label'))}}
            <a class="btn btn-link pull-right" style="padding-top: 0px; padding-right:0px" href="{{ route('password.request') }}">
              {{ __('Forgot Your Password?') }}
            </a>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</main>
@endsection

@section('script')
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
@endsection