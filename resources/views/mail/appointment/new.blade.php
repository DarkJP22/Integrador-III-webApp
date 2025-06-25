<x-mail::message>
## Estimado(a) Dr(a): {{ $appointment->user?->name ?? $appointment->medic_name }}

Reciba un cordial saludo por parte de <span style="text-transform: uppercase;">{{ config('app.name') }}</span> Sírvase la presente para hacerle llegar la confirmación de una cita :
<x-mail::table>
    |        |        |
    | ------------- | ------------- |
    | <img src="{{ config('app.url').'/img/calendar.png' }}" alt="fecha" style="margin-right: 15px;" /> | Fecha de la cita: <b>{{ $appointment->date->toDateString() }}</b> <br/>  Hora de la cita: <b>{{ Carbon\Carbon::parse($appointment->start)->ToTimeString() }}</b> <br/> Paciente: {{ $appointment->patient->fullname }}    |
    | <img src="{{ config('app.url').'/img/map_picker.png' }}" alt="ubicación" style="margin-right: 15px;" />    | Clínica: {{ $appointment->office?->name }} <br/> Dirección: {{ $appointment->office?->address }} <br/> Teléfono: {{ $appointment->office?->phone }} <br /> Fecha de la solicitud: {{ $appointment->created_at->toDateString() }} <br /> <a href="https://maps.google.com/?q={{ $appointment->office?->lat }},{{ $appointment->office?->lon }}">Abrir en Google Maps</a>     |

</x-mail::table>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>