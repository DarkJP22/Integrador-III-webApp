<!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image tw-mb-4">
          <img src="{{ auth()->user()->avatar_path }}" class="img-circle tw-object-cover tw-object-center" alt="User Image" style="height:30px;width:30px;">
        </div>
        <div class="pull-left info">
          <p>{{ auth()->user()->name }}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU</li>
        <li><a href="{{ url('/')}}"><i class="fa fa-home"></i> <span>Home</span></a></li>
        <li><a href="{{ url('/assistant/agenda')}}"><i class="fa fa-calendar"></i> <span>Agenda Citas</span></a></li>
        <li><a href="{{ url('/assistant/schedules/create')}}"><i class="fa fa-calculator"></i><span>Agenda Prog.</span></a></li>
        <li><a href="{{ url('/assistant/medics')}}"><i class="fa fa-user-md"></i> <span>Medicos</span></a></li>
        <li><a href="{{ url('/assistant/patients')}}"><i class="fa fa-users"></i> <span>Pacientes</span></a></li>
        <li><a href="{{ url('/assistant/invoices')}}"><i class="fa fa-money"></i> <span>Facturación</span></a></li>
        <li><a href="{{ url('/assistant/proformas')}}"><i class="fa fa-money"></i> <span>Proformas</span></a></li>
        <li><a href="{{ url('/assistant/cxc')}}" title="Cuentas por cobrar"><i class="fa fa-money"></i> <span>CxC</span></a></li>
        
        <li class="treeview">
          <a href="#"><i class="fa fa-table"></i> <span>Reportes</span> <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span></a>
          <ul class="treeview-menu">
            <li><a href="{{ url('/assistant/cierres') }}"><i class="fa fa-circle-o"></i> Cierres </a></li>
             <li><a href="{{ url('/assistant/balance') }}"><i class="fa fa-circle-o"></i> Último Cierre del día</a></li>
             <li><a href="{{ url('/assistant/cxc/payments')}}" title="Historial de Abonos"><i class="fa fa-circle-o"></i> <span>Historial de abonos</span></a></li>
            
          </ul>
        
        </li>
      
        
    
        
        <li><a href="#" data-toggle="modal" data-target="#contact-modal" data-user=""><i class="fa fa-phone"></i> <span>Contácto / Soporte</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>