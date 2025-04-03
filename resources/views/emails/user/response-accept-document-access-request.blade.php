<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @include('emails.style')
    <style>
        .button a{
            font-size: 14px;
            letter-spacing: 1px;
            color:#fff;
            text-transform: capitalize;
        }
    </style>
</head>

<body>
    <div id="box">
        <div class="text">
            <h1 class="title">Hello {{ $client_name }}</h1>
            <p>{{ $message_text }}</p>
            <div class="button">
                <a href="{{ $dashboard_path }}">Click here to log in to your account</a>
            </div>
            <p>You can now view the documents by navigating to <strong>User Profile -> Documents Tab</strong> in your account.</p>
            <br><br>
            <p>
                Thank you,<br>
                {{ config('app.name') }}
            </p>
        </div>
    </div>

</body>

</html>