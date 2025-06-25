@component('mail::message')
# Un usuario has realizado el cambio al paquete de Perfil de farmacia + facturación

Puedes ponerte en contacto con el para la configuración de su sistema de facturación

# Usuario
- Numero de operación: {{ $purchaseOperationNumber }}
- Correo: {{ $user->email }} <br>
- Teléfono: {{ $user->phone_number }}

Gracias,<br>
{{ config('app.name') }}
@endcomponent