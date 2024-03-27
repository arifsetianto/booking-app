<x-mail::message>
# Hi, {{ $user->name }}

We apologise, your order #{{ $order->code }} has not been approved by us for the following reasons:

<x-mail::panel>
{!! $order->reason !!}
</x-mail::panel>

Please book again with valid data, so we can verify your booking data properly.

Warm regards,<br>
Admin ThaiQuran
</x-mail::message>
