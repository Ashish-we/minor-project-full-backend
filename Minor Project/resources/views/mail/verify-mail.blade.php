<x-mail::message>
# Verify your email

Please verify your email to use the facilities such as apply for job etc.

<x-mail::button :url="'http://127.0.0.1:8000/api/email/verified'">
verify
</x-mail::button>

Thanks,<br>
{{ 'Mero Internship'}}
</x-mail::message>
