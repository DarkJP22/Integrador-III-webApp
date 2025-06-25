@component('mail::message')
# Tu usuario ha sido activado

Tu usuario ha sido activado. ya puedes iniciar sesión y hacer uso del sistema

- Nombre: {{ $user->name }} 
- Correo: {{ $user->email }}

@if($user->isMedic())
@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
Ir a la plataforma
@endcomponent
@endif
Gracias,<br>
{{ config('app.name') }}
@endcomponent
