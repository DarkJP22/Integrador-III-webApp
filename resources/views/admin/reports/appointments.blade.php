@extends('layouts.admins.app')

@section('content')
    <section class="content-header">
        <h1>Reporte de citas reservadas</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <form action="/admin/appointments" method="GET" autocomplete="off">
                            <div class="form-group">


                                <div class="col-sm-3">


                                    <select name="month" id="month" class="form-control" required>

                                        @foreach ($months as $key => $month)
                                            <option value="{{ $key }}" {{ isset($search) && $search['month'] == $key ? 'selected' : '' }}>{{ $month }}</option>
                                        @endforeach

                                    </select>


                                </div>
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
                                <th>Médico</th>
                                <th>Citas</th>
                                <th>Solicitudes de Citas</th>
                                <th>Comisión</th>
                                <th>Ingreso</th>
                                <th>Citas en promo</th>
                                <th>Descuento</th>
                                <th>Total Ingresado</th>
                                <th>Estado</th>

                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['medics'] as $medic)
                                    <tr>
                                        <td>{{ $medic['name'] }}</td>
                                        <td>{{ $medic['appointments_count'] }}</td>
                                        <td>{{ $medic['appointment_requests_count'] }}</td>
                                        <td>{{ money($medic['commission']) }}</td>
                                        <td>{{ money($medic['commission_amount']) }}</td>
                                        <td>{{ $medic['appointment_requests_promo_count'] }}</td>
                                        <td>
                                            {{ money($medic['commission_discount_amount']) }} <br>
                                            <small>%{{ $medic['commission_discount_porc'] }}</small>
                                        </td>
                                        <td>{{ money($medic['commission_total']) }}</td>
                                        <td>
                                            <span @class([
                                            'label',

                                            'label-success' => $medic['status'] === 'Al Dia',
                                            'label-danger' => $medic['status'] === 'Pendiente',
                                        ])>
                                            {{ $medic['status'] }}
                                            </span>

                                        </td>
                                    </tr>

                                @endforeach
                                <tr>
                                    <td class="">

                                    </td>
                                    <td class="" >
                                        {{ $data['totalAppointmentsCount'] }}
                                    </td>
                                    <td class="" >
                                        {{ $data['totalAppointmentRequestsCount'] }}
                                    </td>
                                    <td class="" >

                                    </td>
                                    <td class="" >

                                    </td>
                                    <td class="" >

                                    </td>
                                    <td class="" >
                                    Total Ingreso:
                                    </td>
                                    <td class="" >
                                        {{ money($data['totalIncomes']) }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->


            </div>
        </div>

    </section>

@endsection


