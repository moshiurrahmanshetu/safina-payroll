    <header class="app-header navbar background_blue">
      <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon background_white"></span>
      </button>
      <a class="navbar-brand" href="javascript:void(0)">  <img src="{{asset('public/img/logo.png')}}" alt="Logo" class="img-responsive top_header_img"> </a>
      <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon background_white"></span>
      </button>
      <ul class="nav navbar-nav d-md-down-none txt_center">
        <li class="nav-item px-3">
          <a class="nav-link" href="javascript:void(0)"><p class="company_name_header">eStore Management System </p></a>
          <span class="company_address">Safina Park & Resort, Godagari, Rajshahi, Bangladesh  
          </span>
        </li>
      </ul>
      <ul class="nav navbar-nav ml-auto">

        <li class="nav-item dropdown">
          <a class="nav-link txt_white" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <img class="img-avatar" src="{{ asset('storage/app/admin/users/'.$auth_user_photo) }}" alt="{{$auth_user_name}}">
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header text-center">
              <strong>Account</strong>
            </div>
            <a class="dropdown-item" href="{{ url('/logout') }}"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            <i class="fa fa-lock"></i> Logout
          </a>
          <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>
          <a class="dropdown-item" href="{{ url('/admin/password') }}">
            <i class="fa fa-shield"></i> Change Password</a>
          <a class="dropdown-item" href="{{ url('/admin/profile') }}">
            <i class="fa fa-shield"></i> Change My Profile</a>  
            <div class="dropdown-divider"></div>
          </div>
        </li>
      </ul>

    </header>