@component('mail::message')
# Encuesta de satisfacción del servicio brindado 

Ayudanos a mejorar. Califica y dejanos tus comentarios en el siguiente link:

@component('mail::button', ['url' => config('app.url').'/medics/'.$medicId.'/polls'])
Contestar Encuesta
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
