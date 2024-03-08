<x-mail::message>
# Hi, {{ $user->name }}

We apologise, your order has not been approved by us. Please book again with valid data, so we can verify your booking data properly.

<x-mail::button :url="$url">
Check Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
