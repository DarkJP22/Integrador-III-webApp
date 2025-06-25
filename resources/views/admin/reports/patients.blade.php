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
                        <form action="/admin/patients" method="GET" autocomplete="off">
                            <div class="form-group">


                                <div class="col-sm-3 flatpickr">


                                    <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">





                                </div>
                                <div class="col-sm-3 flatpickr">


                                    <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">


                                </div>


                                <button type="submit" class="btn btn-primary">Generar</button>

                            </div>

                        </form>


                        <div class="box-tools">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">


                        @if ($statistics['patients'])
                            <div class="row">

                                <div class="col-xs-12 col-sm-6">
                                    <h3>N° de pacientes: {{ $statistics['totalPatients'] }}</h3>

                                    @foreach ($statistics['patients'] as $patientsProvince)
                                        <div class="col-xs-12">
                                            <!-- small box -->
                                            <div class="small-box bg-{{ trans('utils.provincias_colors.' . $patientsProvince['province']) }}">
                                                <div class="inner">
                                                    <h3>{{ $patientsProvince['items'] }}</h3>

                                                    <p>{{ trans('utils.provincias.' . $patientsProvince['province']) }}</p>
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
                                    <h3 class="box-title">Gráfico de pacientes</h3>
                                    <canvas id="pieChart" style="height:250px"></canvas>
                                </div>
                            </div>
                        @endif



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

            @if ($statistics['patients'])
                let statusesPatients = ['', 'San José', 'Alajuela', 'Cartago', 'Heredia', 'Guanacaste', 'Puntarenas', 'Limón'];
                let statusesColorClassPatients = ['bg-aqua', 'bg-green', 'bg-yellow'];
                let statusesColorPatients = ['', '#00a65a', '#dd4b39', '#f56954', '#00c0ef', '#f39c12', '#605ca8', '#D81B60'];

                let dataPatients = {!! json_encode($statistics['patients']) !!};

                //  axios.get('/appointments-status?start={{ $search['start'] }}&end={{ $search['end'] }}')
                //  .then(({data})=>{



                let labelsPatients = [];
                let dataStatusPatients = [];
                let backgroundColorsPatients = [];

                dataPatients.forEach(element => {

                    labelsPatients.push(statusesPatients[element.province])
                    dataStatusPatients.push(element.items)
                    backgroundColorsPatients.push(statusesColorPatients[element.province])

                });


                var ctx = document.getElementById("pieChart").getContext('2d');
                var myChart2 = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labelsPatients,
                        datasets: [{
                            label: 'Pacientes',
                            data: dataStatusPatients,
                            backgroundColor: backgroundColorsPatients,

                        }]
                    }

                });
            @endif




            //  })
        });
    </script>
@endpush
