<x-mail::message>
# Assalamu'alaikum!

You are trying to login to ThaiQuran.com.
To complete the login process, please click the button below:

<x-mail::button :url="$url">
Login Now
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
