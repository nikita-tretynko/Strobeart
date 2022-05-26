@component('mail::message')
# Hi {{$user->name}}


Please follow next link to reset password:
@component('mail::button', ['url' =>$url])
    Reset your password
@endcomponent

StrobeArt Team
@endcomponent
