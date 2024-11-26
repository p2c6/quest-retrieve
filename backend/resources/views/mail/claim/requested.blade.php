<x-mail::message>
<img src="{{ asset('images/qr-logo.png') }}" width="160" height="40" style="margin-bottom: 20px;">

Lost Item: {{ $item }}

Claimer's name: {{ $claimers_name }}

Description: {{ $item_description }}

Lost On: {{ $where }}

At: {{ $when }}

Claimer's message: {{ $message }}

<span style="color: red; font-size:1rem;">Note: make sure you validate the claimer's identity and its answers.</span>


</x-mail::message>
