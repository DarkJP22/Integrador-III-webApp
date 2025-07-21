<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image tw-mb-4">
                <img src="{{ auth()->user()->avatar_path }}" class="img-circle tw-object-cover tw-object-center"
                    alt="User Image" style="height:30px;width:30px;">
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
            {{-- <li>--}}
            {{-- <a href="{{ url('/lab/appointment-requests/register?pharmacy_code=' . auth()->user()->pharmacies?->first()?->id) }}"--}}
            {{-- target="_blank"><i--}}
            {{-- class="fa fa-home"></i> <span>Solicitud de cita</span></a></li>--}}
            {{-- <li><a href="{{ url('/pharmacy/medics')}}"><i class="fa fa-user-md"></i> <span>Médicos</span></a></li>--}}
            <li><a href="{{ url('/pharmacy/medicines/reminders')}}"><i class="fa fa-medkit"></i> <span>Encargos</span></a></li>
            <li><a href="{{ url('/pharmacy/patients')}}"><i class="fa fa-users"></i> <span>Pacientes</span></a></li>
            <li><a href="{{ url('/pharmacy/marketing')}}"><i class="fa fa-users"></i> <span>Marketing</span></a></li>
            <li><a href="{{ url('/pharmacy/orders')}}"><i class="fa fa-shopping-cart"></i> <span>Órdenes</span><order-badge-Notifications type="NewOrderPharmacie"></order-badge-Notifications></a></li> <!--ruta de proformas/ordenes-->

            <!--ruta de proformas/ordenes-->
            <li><a href="{{ url('/pharmacy/media')}}"><i class="fa fa-play"></i> <span>Biblioteca Educativa</span></a></li>


            {{-- @if(auth()->user()->subscription && !auth()->user()->subscription->cost > 0)
            <li><a href="{{ url('/pharmacy/changeaccounttype')}}"><i class="fa fa-money"></i>
            <span>Punto de venta</span>
            <span class="pull-right-container">
                <small class="label pull-right bg-red">Obtener</small>
            </span>


            </a></li>
            @endif --}}


            <li><a href="#" data-toggle="modal" data-target="#contact-modal" data-user=""><i class="fa fa-phone"></i>
                    <span>Contácto / Soporte</span></a></li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>