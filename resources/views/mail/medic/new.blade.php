@component('mail::message')
# Nuevo Profesional

Se ha registrado un nuevo usuario como {{ $user->type_of_health_professional?->label() }} en el sistema. Verifícalo

- Nombre: {{ $user->name }} 
- Correo: {{ $user->email }}

@component('mail::button', ['url' => config('app.url').'/login', 'color' => 'primary'])
Ir a usuarios
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent

