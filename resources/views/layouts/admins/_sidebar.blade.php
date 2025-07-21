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
            <li><a href="{{ url('/admin/users')}}"><i class="fa fa-users"></i> <span>Usuarios</span></a></li>
            <li><a href="{{ url('/admin/plans')}}"><i class="fa fa-money"></i> <span>Planes de subscripcion</span></a></li>
            <li><a href="{{ url('/admin/subscription/invoices')}}"><i class="fa fa-money"></i> <span>Facturas de subscripciones</span></a></li>
            <li><a href="{{ url('/admin/clinics/requests')}}"><i class="fa fa-home"></i> <span>Integraciones de clínicas</span></a></li>
            <li><a href="{{ url('/admin/clinics/admins/requests')}}"><i class="fa fa-user"></i> <span>Solicitudes de clínica</span></a></li>
            <li><a href="{{ url('/admin/medics/requests')}}"><i class="fa fa-user-md"></i> <span>Solicitudes de médicos</span></a></li>
            <li><a href="{{ url('admin/affiliations/request/affiliate')}}"><i class="fa fa-user-md"></i> <span>Solicitudes de Afiliados</span></a></li>
            <li><a href="{{ url('/mediatags')}}"><i class="fa fa-tag"></i> <span>Etiquetas (Multimedia)</span></a></li>
            <li><a href="{{ url('/taxes')}}"><i class="fa fa-money"></i> <span>Impuestos</span></a></li>
            <li><a href="{{ url('/drugs')}}"><i class="fa fa-heart-o"></i> <span>Medicamentos</span></a></li>
            <li><a href="{{ url('/admin/discounts')}}"><i class="fa fa-money"></i> <span>Descuentos para Usuarios GPS</span></a></li>
    

            <li class="treeview">
                <a href="#"><i class="fa fa-table"></i> <span>Reportes</span> <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span></a>
                <ul class="treeview-menu">
{{--                    <li><a href="{{ url('/admin/incomes') }}"><i class="fa fa-circle-o"></i> Ingresos</a></li>--}}
{{--                    <li><a href="{{ url('/admin/medics') }}"><i class="fa fa-circle-o"></i> Médicos</a></li>--}}
{{--                    <li><a href="{{ url('/admin/clinics') }}"><i class="fa fa-circle-o"></i> Clínicas</a></li>--}}
{{--                    <li><a href="{{ url('/admin/patients') }}"><i class="fa fa-circle-o"></i> Pacientes</a></li>--}}
                    <li><a href="{{ url('/admin/appointments') }}"><i class="fa fa-circle-o"></i> Citas Reservadas</a></li>
                    <li><a href="{{ url('/admin/annual-performance') }}"><i class="fa fa-circle-o"></i> Comportamiento Anual</a></li>


                </ul>

            </li>


            <li><a href="{{ url('/admin/app/reviews')}}"><i class="fa fa-star"></i> <span>Calificaciones App Movil</span></a></li>

            <li><a href="#" data-toggle="modal" data-target="#contact-modal" data-user=""><i class="fa fa-phone"></i> <span>Contácto / Soporte</span></a></li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside> 