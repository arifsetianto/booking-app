<x-mail::message>
# Hi, {{ $user->name }}

Thank you for your purchase with ThaiQuran.
Please upload your proof of transfer with click the button below, so we can process your order.

<x-mail::button :url="$url">
Confirm Payment
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
