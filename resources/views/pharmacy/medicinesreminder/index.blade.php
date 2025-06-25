@extends('layouts.pharmacies.app')

@section('content')

    <section class="content-header">
        <h1>Recordatorios de compra de medicamentos</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">


                        <form action="/pharmacy/medicines/reminders" method="GET" autocomplete="off">
                            <div class="form-group">


                                <div class="col-sm-3">
                                    <div class="input-group input-group">


                                        <input type="text" name="q" class="form-control pull-right"
                                               placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i>
                                            </button>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-sm-2 flatpickr">


                                    <input data-input type="text" name="start" class="date form-control"
                                           placeholder="Fecha Desde" value="{{ $search['start'] }}">


                                </div>
                                <div class="col-sm-2 flatpickr">


                                    <input data-input type="text" class="date form-control" placeholder="Fecha Hasta"
                                           name="end" value="{{ $search['end'] }}">


                                </div>
                                <div class="col-sm-2">
                                    <select name="status" id="status" class="form-control">
                                        <option value="-1">-- Filtro por estado --</option>
                                        @foreach($medicineReminderStatuses as $status)
                                            <option value="{{ $status['id'] }}" {{ isset($search) && $search['status'] == $status['id'] ? 'selected' : '' }}>{{ $status['name'] }}</option>
                                        @endforeach


                                    </select>

                                </div>
                                <div class="col-sm-3">


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


                                <th>Medicamento</th>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Estado / Respuesta</th>

                                <th></th>
                            </tr>
                            </thead>
                            @foreach($reminders as $reminder)
                                <tr>


                                    <td data-title="Nombre">

                                        {{ $reminder->name }}

                                        {{--                                        {{ $reminder->generated }}--}}
                                    </td>
                                    <td data-title="Cantidad">{{ $reminder->date?->toDateString() }} {{ $reminder->date_purchase }}</td>
                                    <td data-title="Pacientes" class="tw-flex tw-gap-2">

                                        @foreach($reminder->patients as $patient)
                                            <a href="/pharmacy/patients/{{$patient->id}}?tab=medicines">
                                                <span class="tw-block">
                                                    {{ $patient->first_name }}
                                                </span>
                                                <span>Tel: {{ $patient->phone_number }}</span>
                                                {{--                                                <span> Ced: {{ $patient->ide }}</span>--}}
                                            </a>
                                        @endforeach

                                    </td>
                                    <td data-title="Estado / Respuesta">
                                        @if(get_class($reminder) === \App\MedicineReminder::class)

                                            <span class="label label-{{ $reminder->status?->color() }}">{{ $reminder->status?->label() }}</span>

                                        @endif

                                    </td>
                                    <td data-title="" style="padding-left: 5px;">
                                        <div class="tw-flex tw-gap-2">
                                        @if($reminder->generated && $reminder->status_notification === \App\Enums\MedicineReminderStatusNotification::NOT_SENT )
                                            <send-medicine-reminder
                                                    default-message="Hola {{ explode(" ",$reminder->patients?->first()?->first_name)[0] }}, ¿Cómo le va? Le escribimos de la {{ $pharmacy->name }}. Queríamos informarle que tenemos su medicamento {{ $reminder->name }} disponible. Por si lo necesita, con gusto se lo reservamos."
                                                    reminder-id="{{ $reminder->id }}"></send-medicine-reminder>
                                        @endif
                                        @if($reminder->generated && $reminder->status === \App\Enums\MedicineReminderStatus::NO_CONTACTED )
                                            <form action="/pharmacy/medicines/reminders/{{ $reminder->id }}/contacted" method="post" name="form-{{$reminder->id}}">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Contactado</button>
                                            </form>
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($reminders)
                                <td colspan="5"
                                    class="pagination-container">{!!$reminders->appends(['q' => $search['q'], 'start' => $search['start'], 'end' => $search['end'], 'status' => $search['status']])->render()!!}</td>
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

