@extends('layouts.medics.app')

@section('content')
    <section class="content-header">
        <h1>Estados de citas</h1>

    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        @include('agenda._buttons')

                    </div>

                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">


                        <form action="/appointments-status" method="GET" autocomplete="off">
                            <div class="form-group">


                                <div class="col-sm-3 flatpickr">


                                    <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">


                                </div>
                                <div class="col-sm-3 flatpickr">


                                    <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">


                                </div>
                                <button type="submit" class="btn btn-primary">Buscar</button>

                            </div>

                        </form>
                        <div class="box-tools">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="row">
                            @if ($data)
                                <div class="col-xs-12 col-sm-6">
                                    <h3 class="box-title">N° de citas: {{ $data->totalAppointments }}</h3>

                                    <div class=" col-xs-12">
                                        <!-- small box -->
                                        <div class="small-box bg-{{ trans('utils.status_appointments_name_color.attended') }}">
                                            <div class="inner">
                                                <h3>{{ $data->appointments->attended }}</h3>

                                                <p>{{ trans('utils.status_appointments_name.attended') }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>
                                    <div class=" col-xs-12">
                                        <div class="small-box bg-{{ trans('utils.status_appointments_name_color.noassist') }}">
                                            <div class="inner">
                                                <h3>{{ $data->appointments->noassist }}</h3>

                                                <p>{{ trans('utils.status_appointments_name.noassist') }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>
                                    <div class=" col-xs-12">
                                        <div class="small-box bg-{{ trans('utils.status_appointments_name_color.reservedByMedic') }}">
                                            <div class="inner">
                                                <h3>{{ $data->appointments->reservedByMedic }}</h3>

                                                <p>{{ trans('utils.status_appointments_name.reservedByMedic') }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>
                                    <div class=" col-xs-12">
                                        <div class="small-box bg-{{ trans('utils.status_appointments_name_color.reservedByPatient') }}">
                                            <div class="inner">
                                                <h3>{{ $data->appointments->reservedByPatient }}</h3>

                                                <p>{{ trans('utils.status_appointments_name.reservedByPatient') }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>

                                    {{-- @foreach ($data['data'] as $appointmentStatus)
                              <div class=" col-xs-12">
                                
                                    <!-- small box -->
                                    <div class="small-box bg-{{ trans('utils.status_appointments_color.' . $data['status']) }}">
                                      <div class="inner">
                                        <h3>{{ $appointmentStatus['items'] }}</h3>

                                        <p>{{ trans('utils.status_appointments.' . $appointmentStatus['status']) }}</p>
                                      </div>
                                      <div class="icon">
                                        <i class="fa fa-edit"></i>
                                      </div>
                                      <div class="small-box-footer"></div>
                         
                                    </div>
                            </div>
                            @endforeach --}}
                                    <div class="col-xs-12">
                                        <h3 class="box-title">Gráfico</h3>
                                        <canvas id="pieChart" style="height:250px"></canvas>

                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <h3 class="box-title">N° de solicitudes: {{ $data->totalRequests }}</h3>

                                    <div class=" col-xs-12">
                                        <!-- small box -->
                                        <div class="small-box bg-{{ trans('utils.status_appointment_requests_name_color.scheduled') }}">
                                            <div class="inner">
                                                <h3>{{ $data->appointmentRequests->scheduled }}</h3>

                                                <p>{{ trans('utils.status_appointment_requests_name.scheduled') }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>
                                    <div class=" col-xs-12">
                                        <div class="small-box bg-{{ trans('utils.status_appointment_requests_name_color.cancelled') }}">
                                            <div class="inner">
                                                <h3>{{ $data->appointmentRequests->cancelled }}</h3>

                                                <p>{{ trans('utils.status_appointment_requests_name.cancelled') }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>
                                    <div class=" col-xs-12">
                                        <div class="small-box bg-{{ trans('utils.status_appointment_requests_name_color.pending') }}">
                                            <div class="inner">
                                                <h3>{{ $data->appointmentRequests->pending }}</h3>

                                                <p>{{ trans('utils.status_appointment_requests_name.pending') }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>
                                    <div class=" col-xs-12">
                                        <div class="small-box bg-{{ trans('utils.status_appointment_requests_name_color.reserved') }}">
                                            <div class="inner">
                                                <h3>{{ $data->appointmentRequests->reserved }}</h3>

                                                <p>{{ trans('utils.status_appointment_requests_name.reserved') }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="small-box-footer"></div>

                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <h3 class="box-title">Gráfico</h3>
                                        <canvas id="pieChartAppointmentRequests" style="height:250px"></canvas>

                                    </div>
                                </div>
                            @endif
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
        $(function () {

            axios.get('/appointments-status?start={{ $search['start'] }}&end={{ $search['end'] }}')
                .then(({
                           data
                       }) => {

                    let labels = [];
                    let dataStatus = [];
                    let backgroundColors = [];

                    let labelsRequests = [];
                    let dataStatusRequests = [];
                    let backgroundColorsRequests = [];


                    labels.push('Reservadas por pacientes')
                    dataStatus.push(data.appointments.reservedByPatient)
                    backgroundColors.push('#f39c12')

                    labels.push('Reservadas por médico')
                    dataStatus.push(data.appointments.reservedByMedic)
                    backgroundColors.push('#3498db')

                    labels.push('Atendidas')
                    dataStatus.push(data.appointments.reservedByPatient)
                    backgroundColors.push('#00a65a')

                    labels.push('No Asistió')
                    dataStatus.push(data.appointments.noassist)
                    backgroundColors.push('#dd4b39')


                    // data.data.forEach(element => {

                    //     labels.push(statuses[element.status])
                    //     dataStatus.push(element.items)
                    //     backgroundColors.push(statusesColor[element.status])

                    // });


                    var ctx = document.getElementById("pieChart").getContext('2d');
                    var myChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Consultas',
                                data: dataStatus,
                                backgroundColor: backgroundColors,

                            }]
                        }

                    });

                    labelsRequests.push('Reservadas')
                    dataStatusRequests.push(data.appointmentRequests.reserved)
                    backgroundColorsRequests.push('#3498db')

                    labelsRequests.push('Pendientes')
                    dataStatusRequests.push(data.appointmentRequests.pending)
                    backgroundColorsRequests.push('#f39c12')

                    labelsRequests.push('Agendadas')
                    dataStatusRequests.push(data.appointmentRequests.scheduled)
                    backgroundColorsRequests.push('#00a65a')

                    labelsRequests.push('Canceladas')
                    dataStatusRequests.push(data.appointmentRequests.cancelled)
                    backgroundColorsRequests.push('#dd4b39')

                    var ctxAppointmentRequests = document.getElementById("pieChartAppointmentRequests").getContext('2d');
                    var myChart2 = new Chart(ctxAppointmentRequests, {
                        type: 'pie',
                        data: {
                            labels: labelsRequests,
                            datasets: [{
                                label: 'Solicitudes',
                                data: dataStatusRequests,
                                backgroundColor: backgroundColorsRequests,

                            }]
                        }

                    });


                })


        });
    </script>
@endpush
