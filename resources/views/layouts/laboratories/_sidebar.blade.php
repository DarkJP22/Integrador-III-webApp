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
        <li><a href="{{ url('/lab/appointment-requests')}}"><i class="fa fa-user-md"></i> <span>Solicitudes de citas</span> <badge-notifications type="NewAppointmentVisit"></badge-notifications></a></li>
        <li><a href="{{ url('/lab/quotes')}}"><i class="fa fa-edit"></i> <span>Cotizaciones de boletas</span></a></li>
        <li><a href="{{ url('/lab/medics')}}"><i class="fa fa-user-md"></i> <span>Médicos</span></a></li>
        <li><a href="{{ url('/lab/patients')}}"><i class="fa fa-users"></i> <span>Pacientes</span></a></li>
        <li><a href="{{ url('/lab/invoices')}}"><i class="fa fa-money"></i> <span>Facturación</span></a></li>
        <li><a href="{{ url('/lab/proformas')}}"><i class="fa fa-money"></i> <span>Proformas</span></a></li>
        <li><a href="{{ url('/lab/cxc')}}" title="Cuentas por cobrar"><i class="fa fa-money"></i> <span>CxC</span></a></li>
        @if(auth()->user()->getObligadoTributario())
        <li><a href="{{ url('/lab/receptor/mensajes')}}"><i class="fa fa-check"></i> <span>Confirmar Comprobantes</span></a></li>
        @endif
        <li><a href="{{ url('/lab/exams')}}" title="Exámenes"><i class="fa fa-tag"></i> <span>Examenes</span></a></li>
        <li><a href="{{ url('/lab/exams-packages')}}" title="Paquetes de exámenes"><i class="fa fa-tag"></i> <span>Paquetes Exam.</span></a></li>
        <li><a href="{{ url('/lab/visits')}}" title="Visitas a domicilio"><i class="fa fa-tag"></i> <span>Visitas a domicilio</span></a></li>
        <li class="treeview">
          <a href="#"><i class="fa fa-table"></i> <span>Reportes</span> <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span></a>
          <ul class="treeview-menu">
            <li><a href="{{ url('/lab/cierres') }}"><i class="fa fa-circle-o"></i> Cierres </a></li>
            <li><a href="{{ url('/lab/sales') }}"><i class="fa fa-circle-o"></i> Reporte Ventas</a></li>
            <li><a href="{{ url('/lab/commission/labs/pending') }}"><i class="fa fa-circle-o"></i> Comisión Médicos</a></li>
            <li><a href="{{ url('/lab/commission/labs') }}"><i class="fa fa-circle-o"></i> Comisión Médicos (Generadas)</a></li>
          
            
          </ul>
        
        </li>
        
        
        <li><a href="#" data-toggle="modal" data-target="#contact-modal" data-user=""><i class="fa fa-phone"></i> <span>Contácto / Soporte</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>