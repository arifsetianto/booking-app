<x-mail::message>
# Hi, {{ $user->name }}

We apologise, your order #{{ $order->code }} has been canceled by us for the following reasons:

<x-mail::panel>
{!! $order->reason !!}
</x-mail::panel>

Warm regards,<br>
Admin ThaiQuran
</x-mail::message>
