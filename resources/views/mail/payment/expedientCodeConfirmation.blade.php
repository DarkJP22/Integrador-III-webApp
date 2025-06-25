@component('mail::message')
# Confirmación de pago 

-Numero de operación: {{ $purchaseOperationNumber }} 

@component('mail::table')
| Cant. | Descripción         | Subtotal                           |
| -----:|:-------------------:| ----------------------------------:|
| 1     | {{ $description }} Código: {{ $code }} | {{ money($amount)}}       |
|       |     <b>Total:</b>   | <b>{{ money($amount)}}</b>|
@endcomponent


Gracias,<br>
{{ config('app.name') }}
@endcomponent