@component('mail::message')
# Comprobante de proforma {{ $proforma->consecutivo }}

Adjunto PDF con la Factura completa {{ $proforma->consecutivo }}

<b>Consecutivo:</b> {{ $proforma->consecutivo }} <br>
<b>Fecha:</b> {{ $proforma->created_at }}<br>
<b>Cliente:</b> {{ $proforma->cliente }}<br>
<b>Total:</b> {{ money($proforma->TotalComprobante,'') }} {{ $proforma->CodigoMoneda }}<br>

Gracias,<br>
{{ config('app.name') }}
@endcomponent