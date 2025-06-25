@extends('layouts.medics.app')

@section('content')
    <section class="content-header">
        <h1>Agenda del dia</h1>

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


                        <form action="/agenda" method="GET" autocomplete="off">
                            <div class="form-group">

                                <div class="col-sm-2">
                                    <div class="input-group input-group-sm">


                                        <input type="text" name="q" class="form-control" placeholder="Paciente..."
                                               value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>


                                    </div>

                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group input-group-sm flatpickr">


                                        <input type="text" name="date" class="date form-control" placeholder="Fecha..."
                                               value="{{ isset($search) ? $search['date'] : '' }}" data-input>
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>


                                    </div>

                                </div>
                                <div class="col-sm-3">
                                    <select name="office" id="office" class="form-control">
                                        <option value="">-- Filtro por consultorio --</option>
                                        @foreach (auth()->user()->offices as $userClinic)
                                            <option value="{{ $userClinic->id }}" {{ isset($search) && $search['office'] == $userClinic->id ? 'selected' : '' }}>{{ $userClinic->name }}</option>
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
                                <th>Paciente</th>
                                <th>Motivo</th>
                                <th>Fecha</th>
                                <th>De</th>
                                <th>A</th>
                                <th></th>
                            </tr>
                            </thead>

                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td data-title="ID">{{ $appointment->id }}</td>
                                    <td data-title="Paciente">
                                        @if ($appointment->status === App\Enums\AppointmentStatus::CANCELLED)
                                            {{ $appointment->patient ? $appointment->patient->fullname : 'Paciente Eliminado' }}
                                        @else

                                            <a href="{{ (auth()->user()->type_of_health_professional === \App\Enums\TypeOfHealthProfessional::MEDICO) ? url('/agenda/appointments/' . $appointment->id) : '#' }}"
                                               title="{{ $appointment->patient ? $appointment->patient->fullname : 'Paciente Eliminado' }}">{{ $appointment->patient ? $appointment->patient->fullname : 'Paciente Eliminado' }}</a>

                                        @endif
                                    </td>
                                    <td data-title="Motivo">{{ $appointment->title }}</td>
                                    <td data-title="Fecha">{{ \Carbon\Carbon::parse($appointment->date)->toDateString() }}</td>
                                    <td data-title="De">{{ \Carbon\Carbon::parse($appointment->start)->format('h:i:s A') }}</td>
                                    <td data-title="a">{{ \Carbon\Carbon::parse($appointment->end)->format('h:i:s A') }}</td>
                                    <td data-title="" style="padding-left: 5px;">

                                        <div class="btn-group">

                                            @if ($appointment->status === \App\Enums\AppointmentStatus::CANCELLED)
                                                <span class="label label-danger">Cancelada</span>
                                            @else
                                                @if ($appointment->status === \App\Enums\AppointmentStatus::SCHEDULED && \Carbon\Carbon::now()->ToDateString() > $appointment->date)
                                                    <span class="label label-warning"
                                                          style="margin-left: 5px;margin-top: 8px;display: inline-block;">Cita perdida</span>
                                                @else
                                                    @if(auth()->user()->type_of_health_professional === \App\Enums\TypeOfHealthProfessional::MEDICO)
                                                        <a href="{{ url('/agenda/appointments/' . $appointment->id) }}"
                                                           class="btn btn-primary"
                                                           title="{{ $appointment->status === \App\Enums\AppointmentStatus::SCHEDULED ? 'Iniciar Consulta' : 'Ver consulta' }}"><i
                                                                    class="fa fa-eye"></i> {{ $appointment->status === \App\Enums\AppointmentStatus::SCHEDULED ? 'Iniciar Consulta' : ($appointment->revalorizar == 1 ? 'Revaluar' : 'Ver Consulta') }}
                                                        </a>
                                                    @endif
                                                @endif

                                                @if ($appointment->status === \App\Enums\AppointmentStatus::SCHEDULED && $appointment->isOwner())
                                                    <button type="submit" class="btn btn-danger" form="form-delete"
                                                            formaction="{!! url('/agenda/appointments/' . $appointment->id) !!}">
                                                        <i class="fa fa-remove"></i></button>
                                                @endif
                                            @endif
                                        </div>


                                    </td>
                                </tr>
                            @endforeach
                            <tr>

                                @if ($appointments)
                                    <td colspan="7"
                                        class="pagination-container">{!! $appointments->appends(['q' => $search['q'], 'date' => $search['date'], 'office' => $search['office']])->render() !!}</td>
                                @endif


                            </tr>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>

    @include('layouts._modal-subscriptions')
    @include('layouts._modal-pending-payments')


    <form method="post" id="form-delete" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>
    <form method="post" id="form-noshows" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="PUT">{{ csrf_field() }}
    </form>
@endsection
@push('scripts')
    <script>
        $(function () {
            $('#office').on('change', function (e) {


                $(this).parents('form').submit();

            });
        });
    </script>
@endpush
