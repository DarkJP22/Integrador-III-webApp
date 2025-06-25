<!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image tw-mb-4">
          <img src="{{ auth()->user()->avatar_path }}" class="img-circle tw-object-cover tw-object-center" alt="User Image" style="height:30px;width30px;">
        </div>
        <div class="pull-left info">
          <p>{{ auth()->user()->name }}</p>
        <a href="#"><i class="fa fa-circle text-success"></i> {{ Optional(auth()->user()->roles->first())->name }}</a>
        </div>
      </div>
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU</li>
        <li><a href="{{ url('/')}}"><i class="fa fa-home"></i> <span>Home</span></a></li>
        <li><a href="{{ url('/beautician/agenda')}}"><i class="fa fa-calendar"></i> <span>Agenda</span></a></li>
        <li><a href="{{ url('/beautician/schedules/create')}}"><i class="fa fa-calculator"></i> <span>Programe su agenda</span></a></li>
        <li><a href="{{ url('/beautician/patients')}}"><i class="fa fa-users"></i> <span>Pacientes</span></a></li>
        
        
        <li><a href="#" data-toggle="modal" data-target="#contact-modal" data-user=""><i class="fa fa-phone"></i> <span>Contácto / Soporte</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>