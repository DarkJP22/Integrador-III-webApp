@component('mail::message')
# Has realizo el cambio al paquete de Perfil de farmacia + facturación 

Puedes ponerte en contacto con soporte para proseguir con la configuración de tu sistema de facturación

- Numero de operación: {{ $purchaseOperationNumber }}

# Soporte

@foreach($admins as $admin)
- Correo: {{ $admin->email }} <br>
@endforeach 

Gracias,<br>
{{ config('app.name') }}
@endcomponent