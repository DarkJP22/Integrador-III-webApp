<!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image tw-mb-4">
          <img src="{{ auth()->user()->avatar_path }}" class="img-circle tw-object-cover tw-object-center" alt="User Image" style="height:30px; width:30px;">
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
        <li><a href="{{ url('/agenda')}}"><i class="fa fa-calendar"></i> <span>Agenda</span></a></li>
        <li><a href="{{ url('/schedules/create')}}" class="schedules-menu"><i class="fa fa-calculator"></i> <span>Programe su agenda</span></a></li>
        <li><a href="{{ url('/medic/offices')}}" class="offices-menu"><i class="fa fa-hospital-o"></i> <span>Consultorios</span></a></li>
        <li><a href="{{ url('/medic/patients')}}"><i class="fa fa-users"></i> <span>Pacientes</span></a></li>
        
        <li><a href="{{ url('/medic/assistants')}}" class="{{ !auth()->user()->subscriptionPlanHasAssistant() ? 'tippyTooltipAssistants' : '' }}" title="{{ !auth()->user()->subscriptionPlanHasAssistant() ? 'Para obtener este modulo tienes que cambiar de subscripción. Dale Click para hacerlo ahora' : 'Asistentes' }}"><i class="fa fa-user"></i> <span>Asistentes</span> 
        @if(!auth()->user()->subscriptionPlanHasAssistant())
          <span class="pull-right-container">
                <small class="label pull-right bg-red">Alerta</small>
              </span>
          @endif
          </a></li>
          
        <li>
          @php
            $messageTooltip = 'Facturación';
            if( !auth()->user()->subscriptionPlanHasFe() && !auth()->user()->belongstoCentroMedico()){
              $messageTooltip = 'Para obtener este modulo tienes que cambiar de subscripción. Dale Click para hacerlo ahora';
            }
            if( !auth()->user()->subscriptionPlanHasFe() && auth()->user()->belongstoCentroMedico()){
              $messageTooltip = 'Puedes entrar al modulo facturación por ser parte de una clínica privada, pero solo podras ver los documentos si la clínica te autorizó para lo mismo';
            }
            
          @endphp  
        <a href="{{ url('/medic/invoices')}}" 
              class="{{ ( !auth()->user()->subscriptionPlanHasFe() && !auth()->user()->belongstoCentroMedico() || ( !auth()->user()->subscriptionPlanHasFe() && auth()->user()->belongstoCentroMedico())) ? 'tippyTooltipFacturacion' : '' }}" 
              title="{{ $messageTooltip }}" ><i class="fa fa-money"></i> <span>Facturación</span> 
        @if( !auth()->user()->subscriptionPlanHasFe() && !auth()->user()->belongstoCentroMedico() )
          <span class="pull-right-container">
                <small class="label pull-right bg-red">Alerta</small>
              </span>
          @endif
          @if( !auth()->user()->subscriptionPlanHasFe() && auth()->user()->belongstoCentroMedico() )
          <span class="pull-right-container">
                <small class="label pull-right bg-yellow">Info</small>
              </span>
          @endif
          </a></li>
        <li><a href="{{ url('/medic/proformas')}}" class="{{ ( !auth()->user()->subscriptionPlanHasFe() ) ? 'tippyTooltipProformas' : '' }}" title="{{ ( !auth()->user()->subscriptionPlanHasFe() ) ? 'Para obtener este modulo tienes que cambiar de subscripción. Dale Click para hacerlo ahora' : 'Facturación' }}" ><i class="fa fa-money"></i> <span>Proformas</span> 
        @if( ( !auth()->user()->subscriptionPlanHasFe() ) )
          <span class="pull-right-container">
                <small class="label pull-right bg-red">Alerta</small>
              </span>
          @endif
          </a></li>
          
        @if(auth()->user()->getObligadoTributario())
        <li><a href="{{ url('/receptor/mensajes')}}"><i class="fa fa-money"></i> <span>Confirmar Comprobantes</span></a></li>
        @endif
        <li class="treeview">
          <a href="#"><i class="fa fa-table"></i> <span>Reportes</span> <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span></a>
          <ul class="treeview-menu">
            <li><a href="{{ url('/reports/invoices') }}"><i class="fa fa-circle-o"></i> Facturas</a></li>
            <li><a href="{{ url('/incomes') }}"><i class="fa fa-circle-o"></i> Comisión Doctor Blue</a></li>
            {{-- <li><a href="{{ url('/unbilled') }}"><i class="fa fa-circle-o"></i> Citas No Facturadas</a></li> --}}
            <li><a href="{{ url('/appointments-status') }}"><i class="fa fa-circle-o"></i> Estados de citas</a></li>
            
          </ul>
        
        </li>
        <li><a href="#" data-toggle="modal" data-target="#contact-modal" data-user=""><i class="fa fa-phone"></i> <span>Contácto / Soporte</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>