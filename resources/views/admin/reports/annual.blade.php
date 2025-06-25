@extends('layouts.admins.app')

@section('content')
    <section class="content-header">
        <h1>Reporte Comportamiento Anual</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <form action="/admin/annual-performance" method="GET" autocomplete="off">
                            <div class="form-group">


                                <div class="col-sm-3">


                                    <select name="year" id="year" class="form-control" required>

                                        @foreach ($years as $year)
                                            <option value="{{ $year }}" {{ isset($search) && $search['year'] == $year ? 'selected' : '' }}>{{ $year }}</option>
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

                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th></th>
                                @foreach ($data as $key => $month)
                                    <th>{{ $month['month'] }}</th>
                                @endforeach


                            </tr>
                            </thead>
                            <tbody>
                            @if($data)
                                <tr>
                                    <td>Pacientes</td>
                                    @foreach ($data as $key => $month)

                                        <td>{{ $month['totalPatientsCount'] }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Médicos</td>
                                    @foreach ($data as $key => $month)
                                        <td>{{ $month['totalMedicsCount'] }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Médicos Nuevos</td>
                                    @foreach ($data as $key => $month)
                                        <td>{{ $month['totalNewMedicsCount'] }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Ingresos</td>
                                    @foreach ($data as $key => $month)
                                        <td>{{ money($month['incomes']) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Ingresos Pendientes</td>
                                    @foreach ($data as $key => $month)
                                        <td>{{ money($month['pendingIncomes']) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Médicos al dia</td>
                                    @foreach ($data as $key => $month)
                                        <td>
                                            {{ $month['paidMedicsCount'] }} <br>
                                            {{ $month['paidMedics'] }}%
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Médicos pendientes</td>
                                    @foreach ($data as $key => $month)
                                        <td>
                                            {{ $month['pendingMedicsCount'] }} <br>
                                            {{ $month['pendingMedics'] }}%
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Médicos inactivos (+3 meses)</td>
                                    @foreach ($data as $key => $month)
                                        <td>
                                            {{ $month['inactiveMedicsCount'] }} <br>
                                            {{ $month['inactiveMedics'] }}%
                                        </td>
                                    @endforeach
                                </tr>
                            @endif


                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
                @if($data)
                    <div class="box">
                        <div class="box-header"></div>
                        <div class="box-body" style="height: 500px;">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </section>

@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            const ctx = document.getElementById('myChart');
            const data = {{ Illuminate\Support\Js::from($data) }};
            console.log(data)
            const incomesData = data.map(item => item.incomes);
            const pendingIncomesData = data.map(item => item.pendingIncomes);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    datasets: [{
                        label: 'Ingresos',
                        data: incomesData,
                        borderWidth: 1
                    },
                        {
                            label: 'Pendientes',
                            data: pendingIncomesData,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

@endpush
