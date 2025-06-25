@component('mail::message')
# Nueva Solicitud de visita 

Lugar. {{ $appointment->visit_location }}

- Fecha Solicitada: {{ $appointment->created_at->toDateString() }} 
- Paciente: {{ $appointment->patient->first_name }}


Gracias,<br>
{{ config('app.name') }}
@endcomponent