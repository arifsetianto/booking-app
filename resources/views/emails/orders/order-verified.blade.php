<x-mail::message>
# Hi, {{ $user->name }}

Congratulations!
Your order has been verified by the ThaiQuran team and is currently in the process of being delivered to your place.

<x-mail::button :url="$url">
Check Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
