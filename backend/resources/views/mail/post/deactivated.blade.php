<x-mail::message>
<img src="{{ asset('images/qr-logo.png') }}" width="160" height="40" style="margin-bottom: 20px;">

# Hello, {{ $full_name }}

Your post with the details below has been deactivated at QuestRetrieve.

Lost Item: {{ $item }}

Lost On: {{ $where }}

At: {{ $when }}


If you wish to activate it, please go to our website.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
