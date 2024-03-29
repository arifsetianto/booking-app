<x-mail::message>
# Hi, {{ $user->name }}

You have been invited to book ThaiQuran, checkout the item immediately so you don't run out.

<x-mail::button :url="$url">
Booking Now
</x-mail::button>

Warm regards,<br>
Admin ThaiQuran
</x-mail::message>
