<x-mail::message>
# Hi, {{ $user->name }}

We are thrilled to have you move forward with your ThaiQuran order.
Please Transfer the requested amount and Upload the bank transfer receipt as proof of payment through the link below:

<x-mail::button :url="$url">
Upload Receipt
</x-mail::button>

Your order will be processed as soon as we verify the payment.<br/>
Thank you for your cooperation.

Warm regards,<br>
{{ config('app.name') }}
</x-mail::message>
