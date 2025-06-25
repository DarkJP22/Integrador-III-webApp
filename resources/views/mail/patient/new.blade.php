@component('mail::message')
# Bienvenido

Se ha registrado un nuevo perfil de paciente con los siguientes datos:

- Nombre: {{ $user->name }} 
- Correo: {{ $user->email }}

La contraseña es la generica (numero de teléfono). Si no la recuerdas o quieres cambiarla, recuerda que puedes dar click en el link <a href="{{ config('app.url').'/user/password/reset' }}"><strong>Olvidaste tu contraseña?</strong></a> para cambiar tu contraseña mas segura.

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
    Iniciar Sesion
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
