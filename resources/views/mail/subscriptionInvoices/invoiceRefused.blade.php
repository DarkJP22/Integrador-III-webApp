@component('mail::message')
# Comprobante de Factura Rechazado

La Factura {{ $invoice->invoice_number }} ha sido rechazada. Verifica que sea un comprobante válido

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
    Iniciar Sesión
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
