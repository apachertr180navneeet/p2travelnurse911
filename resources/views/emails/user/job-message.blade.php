<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @include('emails.style')
    <style>
        .button a {
            font-size: 18px;
            letter-spacing: 1px;
            color: #fff;
            text-transform: capitalize;
        }
    </style>
</head>

<body>
    <div id="box">
        <div class="text">
            <h1 class="title">Hello {{ $receiver_name }}</h1>
            <p>You’ve received a new message from <strong>{{ $sender_name }}</strong> regarding the job <strong>{{ $job_title }} (#{{ $job_unique_id }})</strong>.</p>
            <p>To view and respond to the message, click the button below:</p>
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