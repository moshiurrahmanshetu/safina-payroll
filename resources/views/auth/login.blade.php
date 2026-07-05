@extends('layouts.app')

@section('content')
    <div class="col-sm-8 col-sm-offset-1 col-md-6 col-md-offset-2 AdminloginForm">
        <h3 class="text-center"><span style='color:green;'>Login to your account</span></h3>
        {{ Form::open(array('route' => 'login')) }}
        
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-user"></i></div>
               {{Form::email('email',null,array('class' => 'form-control', 'placeholder' => 'Email Address'))}}               
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon group-addon-lock"><i class="fa fa-lock"></i></div>
              {{Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password'))}}

              </div>
            </div>
            <div class="form-group">                          
               {{ Form::submit('Sign in',array('class'=>'btn btn-block btn-primary','name'=>'sign_in'))}}
            </div>
            <div class="form-group">              
             {{ Form::checkbox('remember', null, false, array('class'=>'css-checkbox', 'id'=>'remember'))}}
              {{ Form::label('remember',  'Remember me', array('class'=>'css-label'))}}
              
            <!-- <a class="btn btn-link" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a> -->
                        
            </div>
        {{ Form::close() }}
      </div>
@endsection
