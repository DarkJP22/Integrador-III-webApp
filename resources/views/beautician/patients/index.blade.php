@extends('layouts.beauticians.app')

@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">

    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        @include('beautician.agenda._buttons')

                    </div>

                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <a href="{{ url('/general/patients/create') }}" class="btn btn-primary">Nuevo paciente</a>

                        <div class="box-tools">
                            <form action="/beautician/patients" method="GET" autocomplete="off">
                                <div class="input-group input-group-sm">


                                    <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..."
                                           value="{{ isset($search) ? $search['q'] : '' }}">
                                    <div class="input-group-btn">

                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i>
                                        </button>
                                    </div>


                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding" id="no-more-tables">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <!-- <th>ID</th> -->
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Dirección</th>
                                <th></th>
                            </tr>
                            </thead>
                            @foreach ($patients as $patient)
                                <tr>

                                    <td data-title="Marketing">
                                        <input type="checkbox" name="patients[]" value="{{ $patient->id }}"
                                               class="chk-item">
                                    </td>

                                    <td data-title="Nombre">

                                        <a href="{{ url('/beautician/patients/' . $patient->id) }}"
                                           title="{{ $patient->first_name }}">{{ $patient->first_name }}</a>


                                    </td>
                                    @if ($patient->isPatientOfClinic(auth()->user()->offices->first()))
                                        <td data-title="Teléfono">{{ $patient->phone_number }}</td>
                                        <td data-title="Email">{{ $patient->email }}</td>
                                        <td data-title="Dirección">{{ $patient->provinceAddress }}</td>
                                        <td data-title="" style="padding-left: 5px;">
                                            <!-- <a href="{{ url('/beautician/patients/' . $patient->id) }}" class="btn btn-primary" title="Ver Facturado"><i class="fa fa-eye"></i> Información básica</a> -->

                                            <a href="{{ url('/beautician/patients/' . $patient->id) }}"
                                               class="btn btn-primary" title="Ver Facturado"><i class="fa fa-eye"></i>
                                                Información básica</a>
                                            <a href="{{ url('/beautician/patients/' . $patient->id . '/agenda/treatment-appointments') }}"
                                               class="btn btn-secondary" title="Protocolizar"><i
                                                        class="fa fa-calendar"></i> Protocolizar</a>


                                        </td>
                                    @else
                                        <td data-title="Teléfono"></td>
                                        <td data-title="Email"></td>
                                        <td data-title="Dirección"></td>
                                        <td data-title="" style="padding-left: 5px;">
                                            <add-patient-authorization
                                                    :patient="{{ $patient }}"></add-patient-authorization>


                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            @if ($patients)
                                <td colspan="6"
                                    class="pagination-container">{!! $patients->appends(['q' => $search['q']])->render() !!}</td>
                            @endif
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>


    <form method="post" id="form-delete" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>
@endsection
@push('scripts')
    <script src="/vendor/select2/js/select2.full.min.js"></script>

    <script src="/vendor/moment/min/moment.min.js"></script>

    {{-- <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> --}}
    <script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
    <script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="/vendor/fullcalendar/dist/locale/es.js"></script>

    @vite('resources/js/patients.js')
@endpush
