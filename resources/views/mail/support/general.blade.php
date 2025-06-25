@component('mail::message')


{{ $dataMessage['message'] }}


Gracias,<br>
{{ config('app.name') }}
@endcomponent
