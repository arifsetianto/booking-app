<x-mail::message>
# Salam, {{ $user->name }}

We are reaching out to extend a special invitation to you to book one Thai Quran through our platform. Due to a previous system error in your Order #{{ $order->reference->code }}, we understand that you may have experienced inconvenience, and we sincerely apologize for any disruption this may have caused.
<br><br>
As a gesture of goodwill and appreciation for your patience and understanding, we are offering you the opportunity to Re-order a Thai Quran, simply click on the button below:

<x-mail::button :url="$url">
Re-Order Now
</x-mail::button>

Please note that this invitation is valid for a limited time, so we encourage you to take advantage of it at your earliest convenience.

Warm regards,<br>
Admin ThaiQuran
</x-mail::message>
