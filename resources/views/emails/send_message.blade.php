@component('mail::message')
    New message: {{$text}}
@component('mail::button', ['url' => $url])
    Reply
@endcomponent

StrobeArt Team
@endcomponent
