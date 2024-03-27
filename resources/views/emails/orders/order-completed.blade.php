<x-mail::message>
# Your ThaiQuran Order is on Its Way!

Your order has been dispatched and is on its way to you. Here are the tracking details for your convenience:
<br><br>
Tracking Number: {{ $order->shipping->tracking_code }}<br>
Courier Service: EMS Thai Post
<br><br>
You can track your order here: [<a href="https://track.thailandpost.com/?trackNumber={{ $order->shipping->tracking_code }}" target="_blank">Tracking Link</a>]
<br><br>
Thank you for choosing ThaiQuran.
<br><br>
Warm regards,<br>
Admin ThaiQuran
</x-mail::message>
