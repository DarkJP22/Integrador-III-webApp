@component('mail::message')
# Billing Accounts

We failed to create invoices for some of the accounts!

@component('mail::button', ['url' => config('app.url').'/login', 'color' => 'primary'])
Revisar
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
