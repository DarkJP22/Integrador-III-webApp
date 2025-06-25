@component('mail::message')
# Tienes una consulta sobre {{ $dataMessage['subject'] }} por {{ $dataMessage['user']->name }} ({{ $dataMessage['user']->email }})

{{ $dataMessage['message'] }}


Gracias,<br>
{{ config('app.name') }}
@endcomponent
