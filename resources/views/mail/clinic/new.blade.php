@component('mail::message')
# Nuevo administrador de clínica

Se ha registrado un nuevo usuario como administrador de clínica en el sistema. Verfícalo

- Nombre: {{ $user->name }} 
- Correo: {{ $user->email }}
- Clínica: {{ $office->name }}

@component('mail::button', ['url' => config('app.url').'/login', 'color' => 'primary'])
Ir a usuarios
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
