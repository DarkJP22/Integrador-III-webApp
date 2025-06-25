<div class="row">
    <div class="col-sm-6 col-xs-12">
        <h2>Consultas programadas</h2>
        @forelse($scheduledAppointments as $appointment)
            @can('update', $appointment)
                <a class="info-box cita-item" href="{{ url('agenda/appointments/' . $appointment->id) }}" style="text-align: left;">
                @else
                    <a class="info-box cita-item" href="#" style="text-align: left;">
                    @endcan

                    <span class="info-box-icon bg-primary"><i class="fa fa-calendar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ $appointment->title }} con <small>Dr(a). {{ $appointment->user?->name ?? $appointment->medic_name }}</small></span>
                        <span class="info-box-number">{{ \Carbon\Carbon::parse($appointment->date)->toDateString() }}</span>
                        <span class="info-box-text"><small>{{ \Carbon\Carbon::parse($appointment->start)->format('h:i:s A') }} - {{ \Carbon\Carbon::parse($appointment->end)->format('h:i:s A') }}</small></span>

                    </div>
                    @can('update', $appointment)
                        <button type="submit" class="btn btn-danger btn-sm" form="form-delete" formaction="{!! url('appointments/' . $appointment->id) !!}">Cancelar cita</button>
                    @endcan
                    <!-- /.info-box-content -->
                </a>
            @empty
                <p>Aun no hay citas agendadas.</p>
        @endforelse
    </div>
    <div class="col-sm-6 col-xs-12">
        <h2>Historial de consultas</h2>
        @forelse($initAppointments as $appointment)
            <div class="info-box cita-item" style="text-align: left;margin-bottom: 5px;">
                <span class="info-box-icon bg-default"><i class="fa fa-calendar"></i></span>

                <div class="info-box-content">

                    @can('update', $appointment)
                        <a class="info-box-text" href="/agenda/appointments/{{ $appointment->id }}">{{ $appointment->title }} con <small>Dr(a). {{ $appointment->user?->name ?? $appointment->medic_name }}</small></a>
                    @else
                        <a class="info-box-text" href="#"><small>Dr(a). {{ $appointment->user?->name ?? $appointment->medic_name }}</small></a>
                    @endcan
                    <span class="info-box-number">{{ \Carbon\Carbon::parse($appointment->date)->toDateString() }}<small></small></span>
                    @can('create', $appointment)
                        <a class="btn btn-info" href="/agenda/create?clinic={{ $appointment->office_id }}&p={{ $appointment->patient_id }}">Crear seguimiento</a>
                    @endcan
                </div>

                <!-- /.info-box-content -->
            </div>


            <!-- <notes :notes="{{ $appointment->notes }}" :appointment="{{ $appointment }}" ></notes> -->



        @empty
            <p>Aun no hay citas iniciadas.</p>
        @endforelse
    </div>

</div>
