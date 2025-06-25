@extends('layouts.laboratories.app')

@section('content')
    <section class="content-header">
        <h1>Solicitudes de Citas</h1>
        <a href="/lab/appointment-requests/create" class="btn btn-primary">Nueva Solicitud</a>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <form action="/lab/appointment-requests" method="GET" autocomplete="off">
                            <div class="form-group">

                                <div class="col-sm-3">
                                    <div class="input-group input-group">


                                        <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-sm-2 flatpickr">


                                    <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">


                                </div>
                                <div class="col-sm-2 flatpickr">


                                    <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">


                                </div>
                                <div class="col-sm-2">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">-- Filtro por estado --</option>
                                        @foreach($appointmentRequestStatuses as $status)
                                            <option value="{{ $status['id'] }}" {{ isset($search) && $search['status'] === $status['id'] ? 'selected' : '' }}>{{ $status['name'] }}</option>
                                        @endforeach


                                    </select>

                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
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

                                <th>Paciente</th>
                                <th style="min-width: 200px; width:300px">Visita</th>
                                <th style="min-width: 250px;">Examenes a realizar</th>
                                <th>Estado</th>

                            </tr>
                            </thead>
                            @foreach ($appointmentRequests as $appoRequest)
                                <tr>


                                    <td data-title="Paciente">
                                        <div>
                                            {{ $appoRequest->patient_name }}
                                        </div>
                                        <div>Tel: {{ $appoRequest->phone_number }}</div>
                                        <div>
                                            {{ $appoRequest->provinceName }}, {{ $appoRequest->canton }}, {{ $appoRequest->district }}
                                            @if ($appoRequest->coords)
                                                <lab-appointment-request-share-location :appointment-request="{{ $appoRequest }}"></lab-appointment-request-share-location>
                                            @endif
                                        </div>


                                    </td>
                                    {{-- <td data-title="Teléfono">{{ $appoRequest->phone_number }}</td>
                                    <td data-title="Lugar">
                                        {{ $appoRequest->provinceName }}, {{ $appoRequest->canton }}, {{ $appoRequest->district }}
                                        @if ($appoRequest->coords)
                                            <lab-appointment-request-share-location :appointment-request="{{ $appoRequest }}"></lab-appointment-request-share-location>
                                        @endif
                                    </td> --}}

                                    <td data-title="Lugar Visita">

                                        <lab-appointment-request-update-visit-location :appointment-request="{{ $appoRequest }}"></lab-appointment-request-update-visit-location>

                                    </td>
                                    <td data-title="Examenes a realizar">

                                        <lab-appointment-request-update-exams :appointment-request="{{ $appoRequest }}"></lab-appointment-request-update-exams>

                                    </td>

                                    <td data-title="Estado">
                                        <button @class([
                                            'btn',
                                            'btn-xs',
                                            'btn-success' => $appoRequest->status === \App\Enums\LabAppointmentRequestStatus::COMPLETED,
                                            'btn-danger' => $appoRequest->status === \App\Enums\LabAppointmentRequestStatus::PENDING,
                                        ]) type="submit" form="form-status" formaction="{!! URL::route('appointmentRequest.status', [$appoRequest->id]) !!}">
                                            {{ $appoRequest->status->label() }}
                                        </button>
                                    </td>

                                </tr>
                            @endforeach
                            @if ($appointmentRequests)
                                <td colspan="6"
                                    class="pagination-container">{!! $appointmentRequests->appends(['q' => $search['q'], 'start' => $search['start'], 'end' => $search['end'], 'status' => $search['status']])->render() !!}</td>
                            @endif
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>
    <form method="post" id="form-status">
        {{ csrf_field() }}
    </form>
@endsection
@push('scripts')
    <script>
        $(function() {
            setTimeout(() => {
                window.emitter.emit('clearBadgeNotifications', {type: 'NewAppointmentVisit'});
            }, 1000);

        });
    </script>
@endpush

