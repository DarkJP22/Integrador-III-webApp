<x-mail::message>
@php Illuminate\Support\Number::useCurrency('CRC') @endphp
## Bienvenido {{ $user->name }}

Bienvenido a la plataforma de <span style="text-transform: uppercase;">{{ config('app.name') }}</span>. Tu cuenta esta en periodo de revisión, mientras tanto sírvase la presente para hacerle llegar la confirmación de su plan :
<x-mail::table>
    |  Detalle      |   Costo     |
    | ------------- | ------------- |
    | {{ $user->subscription?->plan?->title }} <br> <small>(Visible para recibir citas)</small> | {{ \Illuminate\Support\Number::currency($user->subscription?->plan?->cost) }} x Més    |
    |  + Comisión por cita   |  {{ $user->specialities->count() ? \Illuminate\Support\Number::currency($user->subscription?->plan?->specialist_cost_commission_by_appointment) : \Illuminate\Support\Number::currency($user->subscription?->plan?->general_cost_commission_by_appointment) }} <br> <small>* Descuento x Cita: {{ Illuminate\Support\Number::percentage($user->subscription?->plan?->commission_discount) }}</small> |

</x-mail::table>
<small>* Para obtener este descuento deberás seleccionar la opción de Utiliza Agenda DOCTOR BLUE al momento de crear un consultorio, esto te permitirá recibir CITAS EN LÍNEA con un 50% menos de comisión. En caso de NO Utilizar la Agenda de DOCTOR BLUE, podrás obtener el mismo descuento colocando al paciente en estatus de AGENDADO en la plataforma. Lo anterior en un plazo inferior a 10min tras recibir la solicitud.</small> <br />
<small>* Una vez aprobado recibirás un correo de confirmación.</small>
    <br /> <br />
Gracias,<br />
{{ config('app.name') }}
</x-mail::message>