<x-mail::message>
# Hi, {{ $user->name }}

Thank you for booking ThaiQuran, the order is being sent to your address.

<x-mail::button :url="$url">
Check Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
