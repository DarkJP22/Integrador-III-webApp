@extends('layouts.admins.app')

@section('content')
    <section class="content-header">
        <h1>Reporte de clínicas</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <form action="/admin/clinics" method="GET" autocomplete="off">
                            <div class="form-group">


                                <div class="col-sm-3 flatpickr">


                                    <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">





                                </div>
                                <div class="col-sm-3 flatpickr">


                                    <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">


                                </div>
                                <div class="col-sm-3">
                                    <select name="clinic" id="clinic" class="form-control" required>
                                        <option value="">-- Filtro por clínica --</option>
                                        @foreach ($clinics as $clinic)
                                            <option value="{{ $clinic->id }}" {{ isset($search) && $search['clinic'] == $clinic->id ? 'selected' : '' }}>{{ $clinic->name }}</option>
                                        @endforeach

                                    </select>

                                </div>

                                <button type="submit" class="btn btn-primary">Generar</button>

                            </div>

                        </form>


                        <div class="box-tools">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">



                        <div class="row">

                            <div class="col-xs-12 col-sm-6">
                                <h3>N° de clinicas: {{ $statistics['totalClinics'] }}</h3>
                                <h3>N° de consultas de la clínica seleccionada: {{ $statistics['totalAppointments'] }}</h3>
                                @foreach ($statistics['appointments'] as $appointmentsStatus)
                                    <div class="col-xs-12">
                                        <!-- small box -->
                                        <div class="small-box bg-{{ trans('utils.status_appointments_color.' . $appointmentsStatus['status']) }}">
                                            <div class="inner">
                                                <h3>{{ $appointmentsStatus['items'] }}</h3>

                                                <p>{{ trans('utils.status_appointments.' . $appointmentsStatus['status']) }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <h3 class="box-title">Gráfico de consultas</h3>
                                <canvas id="pieChartAppointments" style="height:250px"></canvas>
                            </div>
                        </div>



                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>
@endsection
@push('scripts')
    <script src="/vendor/chart.js/Chart.min.js"></script>
    <script>
        $(function() {


            let statuses = ['Inactivos', 'Activos'];
            let statusesColorClass = ['bg-yellow', 'bg-green'];
            let statusesColor = ['#dd4b39', '#00a65a'];
            let statusesAppointments = ['Reservadas', 'Atendidas', 'No Asistió'];
            let statusesColorClassAppointments = ['bg-aqua', 'bg-green', 'bg-yellow'];
            let statusesColorAppointments = ['#f39c12', '#00a65a', '#dd4b39'];

            let dataAppointments = {!! json_encode($statistics['appointments']) !!};

            //  axios.get('/appointments-status?start={{ $search['start'] }}&end={{ $search['end'] }}')
            //  .then(({data})=>{



            let labelsAppointments = [];
            let dataStatusAppointments = [];
            let backgroundColorsAppointments = [];

            dataAppointments.forEach(element => {

                labelsAppointments.push(statusesAppointments[element.status])
                dataStatusAppointments.push(element.items)
                backgroundColorsAppointments.push(statusesColorAppointments[element.status])

            });


            var ctx = document.getElementById("pieChartAppointments").getContext('2d');
            var myChart2 = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labelsAppointments,
                    datasets: [{
                        label: 'Consultas',
                        data: dataStatusAppointments,
                        backgroundColor: backgroundColorsAppointments,

                    }]
                }

            });




            //  })
        });
    </script>
@endpush
