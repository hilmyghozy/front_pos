<nav class="navbar navbar-expand-lg main-navbar navbar-left-menu">
  <ul class="navbar-nav navbar-right mt-1">
    <li class="dropdown"><a href="#" class="nav-link nav-link-lg nav-link-user">
      <div class="d-sm-none d-lg-inline-block h4"><b>{{session('nama_store')}}</b></div></a>
    </li>
  </ul>
</nav>
<nav class="navbar navbar-expand-lg main-navbar navbar-right">
    <ul class="navbar-nav navbar-right">
      @if (session('username'))
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <i class="fas fa-user"></i>
        <div class="d-sm-none d-lg-inline-block">Hi, {{session('username')}}</div></a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="{{ url('lock') }}" class="dropdown-item has-icon text-danger mt-1">
            <i class="fas fa-lock"></i> Lock Screen
          </a>
          <a href="{{ url('close') }}" class="dropdown-item has-icon text-danger mb-1">
            <i class="fas fa-sign-out-alt"></i> Close
          </a>
        </div>
      </li>
      @endif
      
    </ul>
</nav>