@extends('layouts.clinics.app')
@section('header')
    <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
@endsection
@section('content')

    <section class="content-header">
        <h1>Agenda de tratamientos</h1>

    </section>
    <section class="content">


        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $patient->first_name }}</h3>
                
            </div>
            <!-- /.box-header -->
            <div class="box-body">

                <div class="row">
                    <div class="col-xs-12 col-sm-9">
                        <p class="text-muted">
                            {{ trans('utils.gender.' . $patient->gender) }} - Edad: {{ $patient->age }}
                        </p>
                        <p class="text-muted">{{ $patient->phone }}</p>
                    </div>
                    <div class="col-xs-12 col-sm-3 text-right">
                        <a href="/clinic/agenda" class="btn btn-primary">Ver Calendario</a>
                    </div>
                </div>
               

               

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <agenda-treatments :patient="{{ json_encode($patient->load('optreatments')) }}"
                    :office-id="{{ $office->id }}" :appointments="{{ $appointments }}"
                    :esteticistas="{{ $esteticistas }}"
                    :optreatments="{{ $optreatments }}"
                    ></agenda-treatments>
            </div>

        </div>

    </section>

@endsection
@push('scripts')
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/moment/min/locales.min.js"></script>
    <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $(function() {

            $('.datetimepickerAppointment').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
                locale: 'es',

            });
        });
    </script>
@endpush
