<header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <a href="/" class="navbar-brand tw-flex tw-items-center tw-space-x-2 tw-w-[50px]"><img src="/img/logo-white.png" alt="{{ config('app.name', 'Doctor Blue') }}" style="width: 50px;"> </a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
            {{-- <li><a href="{{ url('/medics?typeOfSearch=general') }}" >Buscar Médico General</a></li>
            <li><a href="{{ url('/medics?typeOfSearch=specialist') }}" >Buscar Médico Especialista</a></li>
            <li><a href="{{ url('/clinics') }}" >Buscar Clínica</a></li> --}}
           
            @auth
               @if(auth()->user()->patients->first())
               <li><a href="{{ url('/patients/'. auth()->user()->patients->first()->id .'/expedient') }}" >Expediente</a></li>
               @endif
           
            
            @endauth
          </ul>
          
        </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            @guest
                <li><a class="nav-link" href="{{ url('/user/login') }}">Iniciar sesión</a></li>
{{--                <li><a class="nav-link" href="{{ url('/register') }}">Crear una cuenta</a></li>--}}
            @endguest
            
            <!-- User Account Menu -->
             @auth
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
               
                 <img src="{{ auth()->user()->avatar_path }}" class="user-image" alt="User Image">
               
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs">
                   
                      {{ auth()->user()->name }}
                   
                    
                </span>
              </a>
              <ul class="dropdown-menu">
                <!-- The user image in the menu -->
               
                <li class="user-header">
                  <img src="{{ auth()->user()->avatar_path }}" class="img-circle tw-object-cover tw-object-center" alt="User Image">

                  <p>
                   {{ auth()->user()->name }}
                    <small>Miembro desde hace {{ auth()->user()->created_at->diffForHumans() }}</small>
                  </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body">
                  <div class="row">
                    <div class="col-xs-4 text-center">
                      <a href="#" data-toggle="modal" data-target="#contact-modal" data-user="{{ auth()->user()->email }}">Soporte</a>
                    </div>
                    <div class="col-xs-8 text-center">
                      <a href="{{ url('/appointments') }}">Consultas Programadas</a>
                    </div>
                    
                  </div>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="{{ url('/profiles/'.auth()->id()) }}" class="btn btn-default btn-flat">Perfil</a>
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
            @endauth
          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>