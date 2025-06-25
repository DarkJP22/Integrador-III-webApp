@component('mail::message')
# Nueva Cotización de Boleta Solicitada

El paciente {{ Optional($quoteOrder->patient)->fullname }} ha solicitado una cotización de boleta. Revisala en la pestaña de Cotizaciones de boletas

@component('mail::button', ['url' => config('app.url').'/lab/quotes','color' => 'red'])
    Revisar
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent