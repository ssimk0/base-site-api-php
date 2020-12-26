@component('mail::message')
# {{__('auth.forgot_password_heading')}}

{{__('auth.forgot_password_mail')}}

@component('mail::button', ['url' => env('APP_URL').'/reset-password?token='.$token])
{{__('auth.forgot_password_button')}}
@endcomponent

{{__('mail.sign')}},<br>
{{ config('app.name') }}
@endcomponent
