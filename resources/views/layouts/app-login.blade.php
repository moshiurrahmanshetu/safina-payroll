<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Sahadat">
  <!-- CSRF Token -->
  <link rel="icon" type="image/ico" href="{{ asset('public/img/logo.png') }}" sizes="any" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'eStore Management | Safina Park & Resort, Godagari, Rajshahi, Bangladesh') }}</title>
  <!-- Scripts -->
  <script src="{{ asset('public/js/app.js') }}" defer></script>
  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
  <!-- Styles -->
  <link href="{{asset('public/css/font-awesome.min.css')}}" rel="stylesheet">
  <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
  <link href="{{asset('public/css/login.css')}}" rel="stylesheet">
  <link href="{{asset('public/css/login-custom.css')}}" rel="stylesheet">
</head>
<body>

    <!--for validation error start-->
    <div class="flash-message">
      @foreach (['danger', 'warning', 'success', 'info'] as $msg)
      @if(Session::has('flash_' . $msg))
      <p class="alert alert-{{ $msg }}"><a href="#" class="close" data-dismiss="alert">&times;</a> <strong> {{ $msg }}!!</strong> {{ Session::get('flash_' . $msg) }}</p>
      @endif
      @endforeach
    </div>
    <!--for validation error end-->

      @yield('content')


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
  <script src="{{asset('public/js/jquery-1.10.2.js')}}"></script>
  <!-- only custom script section start -->
    @yield('script')
  <!-- only custom script section start --> 
</body>
</html>