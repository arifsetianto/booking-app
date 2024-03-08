<x-mail::message>
# Hi, {{ $user->name }}

Your order is on its way to you.

<x-mail::button :url="$url">
Check Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
