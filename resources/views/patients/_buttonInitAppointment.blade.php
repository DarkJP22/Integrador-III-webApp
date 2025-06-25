@if(auth()->user()->hasSubscription())
            @if(!$monthlyCharge->count())
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#initAppointment" data-backdrop="static" data-patient="{{ $patient->id }}" data-patientname="{{ $patient->first_name }} {{ $patient->last_name }}" title="Iniciar consulta con este paciente"><i class="fa fa-list"></i> Iniciar Consulta
                    </button>
            @else
                    <a href="#" data-toggle="modal" data-target="#modalPendingPayments" class="btn btn-primary" title="Iniciar Consulta"><i class="fa fa-list"></i> Iniciar Consulta</a>
            @endif
    @else
            <a href="#" data-toggle="modal" data-target="#modalSubscription" class="btn btn-primary" title="Iniciar Consulta"><i class="fa fa-list"></i> Iniciar Consulta</a>
    @endif