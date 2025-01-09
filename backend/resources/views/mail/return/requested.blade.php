<x-mail::message>
<img src="{{ asset('images/qr-logo.png') }}" width="160" height="35" style="margin-bottom: 20px;">

Item: {{ $item }}

Returner's name: {{ $returners_name }}

Description: {{ $item_description }}

Found At: {{ $where }}

On: {{ $when }}

Returner's message: {{ $message }}

<span style="color: red; font-size:1rem;">Note: make sure you validate the returner's identity and its answers.</span>


</x-mail::message>
