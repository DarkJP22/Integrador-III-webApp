<header class="main-header">
  <!-- Logo -->
  <a href="#" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b><img src="/img/logo-white.png" class="tw-h-[40px] tw-object-contain" alt="{{ config('app.name', 'Doctor Blue') }}"></b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b><img src="/img/logo-white.png" class="tw-h-[40px] tw-object-contain" alt="{{ config('app.name', 'Doctor Blue') }}"></b></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">

        <user-notifications></user-notifications>

        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="{{ auth()->user()->avatar_path }}" class="user-image tw-object-cover tw-object-center" alt="User Image">
            <span class="hidden-xs">{{ auth()->user()->name }} - Laboratorio: @foreach(auth()->user()->offices as $clinic) {{ $clinic->name }} @endforeach</span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <img src="{{ auth()->user()->avatar_path }}" class="img-circle tw-object-cover tw-object-center" alt="User Image">

              <p>
                {{ auth()->user()->name }}

              </p>
            </li>
            <!-- Menu Body -->
            <li class="user-body">
              <div class="row">
                <div class="col-xs-4 text-center">
                  <a href="/lab/patients">Pacientes</a>
                </div>

              </div>
              <!-- /.row -->
            </li>
            @foreach(auth()->user()->subaccounts as $account)
            <li class="user-body">
              <div class="row">
                <div class="col-xs-12">
                  <a href="/accounts/{{ $account->id }}/switch" onclick="event.preventDefault();
                                                    document.getElementById('switch-form').submit();">

                    <img src="{{ $account->avatar_path }}" class="user-image" alt="User Image"> {{ $account->name }} <br><small>{{ $account->email }}</small>
                    <button type="button" class="btn btn-secondary pull-right">Cambiar</button></a>
                  <form id="switch-form" action="{{ url('/accounts/'.$account->id.'/switch') }}" method="POST" style="display: none;">
                    @csrf
                  </form>
                </div>

              </div>
              <!-- /.row -->
            </li>
            @endforeach
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <a href="{{ url('/lab/profiles') }}" class="btn btn-default btn-flat">Profile</a>
              </div>
              <div class="pull-right">

                <a class="btn btn-default btn-flat" href="{{ url('/logout') }}"
                  onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                  {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </div>
            </li>
          </ul>
        </li>

      </ul>
    </div>
  </nav>
</header>