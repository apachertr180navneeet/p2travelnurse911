<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @include('emails.style')
</head>

<body>
    <div id="box">
        <div class="text">
            <h1 class="title">Hello {{ $receiver_name }}</h1>
            <p>You have a new message from {{ $sender_name }} on job id #{{ $job_unique_id }}.</p>
            <div class="button">
                <a href="{{ $dashboard_path }}">View Message</a>
            </div>
            <br><br>
            <p>
                Thank you,<br>
                {{ config('app.name') }}
            </p>
        </div>
    </div>

</body>

</html>
