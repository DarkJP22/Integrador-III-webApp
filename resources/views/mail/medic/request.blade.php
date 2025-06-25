@component('mail::message')
# Nueva Solicitud de Médico

Se ha registrado un nuevo médico en tu clínica. Verfícalo

- Nombre: {{ $user->name }} 
- Correo: {{ $user->email }}

@component('mail::button', ['url' => config('app.url').'/login', 'color' => 'primary'])
Ir a medicos
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent

