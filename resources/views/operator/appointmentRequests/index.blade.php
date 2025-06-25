@extends('layouts.operators.app')

@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
@endsection
@section('content')
    <section class="content-header">
        <h1>Solicitudes de citas</h1>

    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <form action="/operator/appointment-requests" method="GET" autocomplete="off" id="form">
                            <div class="form-group">

                                <div class="col-sm-3">
                                    <div class="input-group input-group">


                                        <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <select name="medic" id="medic" class="form-control" onchange="form.submit()">
                                        <option value="">-- Filtro por medico --</option>
                                        @foreach($medics as $medic)
                                            <option value="{{  $medic->id }}" {{ (isset($search) && $search['medic'] == $medic->id) ? 'selected' : '' }}>{{  $medic->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select name="status[]" multiple id="status" class="form-control select2" onchange="form.submit()" >
{{--                                        <option value="">-- Filtro por estado --</option>--}}
                                        @foreach($statuses as $status)
                                            <option value="{{  $status['id'] }}" {{ (isset($search) && $search['status'] != '' && in_array($status['id'], $search['status'])) ? 'selected' : '' }}>{{  $status['name'] }}</option>
                                        @endforeach

                                    </select>
                                </div>


                            </div>

                        </form>
                        <div class="box-tools">

                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding" id="no-more-tables">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Médico</th>
                                <th>Clínica</th>
                                <th>Paciente</th>
                                <th>Estado</th>
                                <th>Creada</th>
                                <th>Respondida</th>
                            </tr>
                            </thead>
                            @foreach ($appointmentRequests as $appointmentRequest)
                                <tr>

                                    <td data-title="ID">{{ $appointmentRequest->id }}</td>
                                    <td data-title="Medico">

                                        {{ $appointmentRequest->medic?->name }}
                                        <div>
                                            <a href="tel:{{ $appointmentRequest->medic?->phone_number }}">{{ $appointmentRequest->medic?->phone_number }}</a>
                                        </div>

                                    </td>
                                    <td data-title="Clinica">
                                        {{ $appointmentRequest->office->name }}
                                        <div>
                                            <a href="tel:{{ $appointmentRequest->office?->phone }}">{{ $appointmentRequest->office?->phone }}</a>

                                        </div>
                                    </td>
                                    <td data-title="Paciente">
                                        {{ $appointmentRequest->patient?->fullname }}
                                        <div>
                                            <a href="tel:{{ $appointmentRequest->patient?->phone_number }}">{{ $appointmentRequest->patient?->phone_number }}</a>
                                        </div>
                                    </td>

                                    <td data-title="Estado">
                                        <span @class([
                                        'label' => true,
                                        'label-success' => $appointmentRequest->status === \App\Enums\AppointmentRequestStatus::SCHEDULED,
                                        'label-warning' => $appointmentRequest->status === \App\Enums\AppointmentRequestStatus::RESERVED,
                                        'label-danger' => $appointmentRequest->status === \App\Enums\AppointmentRequestStatus::PENDING,
                                        'label-dark bg-dark' => $appointmentRequest->status === \App\Enums\AppointmentRequestStatus::CANCELLED
                                        ])>

                                        {{ $appointmentRequest->status->label() }}
                                        </span>


                                    </td>
                                    <td data-title="Creada">
                                        {{ $appointmentRequest->created_at }}

                                    </td>
                                    <td data-title="" style="padding-left: 5px;">
                                        @if($appointmentRequest->status === \App\Enums\AppointmentRequestStatus::SCHEDULED)

                                            {{ $appointmentRequest->scheduled_at?->diffForHumans($appointmentRequest->created_at) }}
                                        @endif


                                    </td>
                                </tr>
                            @endforeach
                            @if ($appointmentRequests)
                                <td colspan="5" class="pagination-container">{!! $appointmentRequests->appends(['q' => $search['q'], 'medic' => $search['medic'], 'status' => $search['status']])->render() !!}</td>
                                <td colspan="2" class="averages tw-text-4xl">Promedio de respuesta: {{ money($averages->average_response, '',0) }} min.</td>
                            @endif
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>

@endsection
@push('scripts')
    <script src="/vendor/select2/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "-- Filtro por estado --",
            });

            setTimeout(() => {
                window.emitter.emit('clearBadgeNotifications', {type: 'NewAppointmentRequestNotification'});
            }, 1000);
        });
    </script>
@endpush

