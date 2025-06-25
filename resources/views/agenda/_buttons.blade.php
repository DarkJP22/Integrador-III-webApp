<div class="buttons-shortcuts">
    <a href="{{ url('/agenda') }}" class="btn btn-primary {{ set_active('agenda') }}">Agenda del dia</a>
    <a href="{{ url('/calendar') }}" class="btn btn-primary {{ set_active('calendar') }}">Calendario Semanal</a>
    <a href="{{ url('/schedules/create') }}" class="btn btn-primary {{ set_active('schedules/create') }}">Programe su agenda</a>
    <a href="{{ url('/general/patients/create') }}" class="btn btn-primary {{ set_active('general/patients/create') }}">Nuevo Paciente</a>
    <a href="{{ url('/agenda/create') }}" class="btn btn-primary {{ set_active('agenda/create') }}">Crear Consulta</a>
    <a href="{{ url('/medic/offices') }}" class="btn btn-primary {{ set_active('medic/profiles') }}">Consultorios</a>
</div>
