<x-mail::message>
# Hi, {{ $user->name }}

Thank you for your payment confirmation.
Please wait, your order is currently being verified by the ThaiQuran team.

<x-mail::button :url="$url">
Check Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
