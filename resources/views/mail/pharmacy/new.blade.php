@component('mail::message')
# Nuevo administrador de farmacia

Se ha registrado un nuevo usuario como administrador de Farmacia en el sistema. Verfícalo

- Nombre: {{ $user->name }} 
- Correo: {{ $user->email }}
- Farmacia: {{ $pharmacy->name }}

@component('mail::button', ['url' => config('app.url').'/login', 'color' => 'primary'])
Ir a usuarios
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent