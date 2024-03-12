<x-mail::message>
# Hi, {{ $user->name }}

Your booking data needs to be corrected as we are unable to verify your booking. To continue the booking process please update your booking data first.

<x-mail::button :url="$url">
Edit My Booking
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
