@component('mail::message')
# Hello {{ $supportMessage->name }},

Our support team has replied to your message.

@component('mail::panel')
**Subject:** {{ $supportMessage->subject }}<br>
**Original Message:** {{ \Illuminate\Support\Str::limit($supportMessage->message, 200) }}
@endcomponent

Log in to read our full response and any follow-up information.

@component('mail::button', ['url' => route('customer.support.show', $supportMessage->id), 'color' => 'green'])
View Reply
@endcomponent

Thank you for contacting Iimaan Dry Cleaner.

Thanks,<br>
{{ config('app.name', 'LOMS') }} Support Team
@endcomponent
