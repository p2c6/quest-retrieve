<x-mail::message>
<img src="{{ asset('images/qr-logo.png') }}" width="160" height="40" style="margin-bottom: 20px;">

# Hello, {{ $full_name }}

Here is the details of your post at QuestRetrieve.

Lost Item: {{ $item }}

Lost On: {{ $where }}

At: {{ $when }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
