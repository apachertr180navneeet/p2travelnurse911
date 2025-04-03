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
            <p>You’ve been requested by <strong>{{ $sender_name }}</strong> to complete a reference form.</p>
            <p>Please help us by providing your feedback and completing the reference form at your earliest convenience. To access the form, click the button below:</p>

            <div class="button">
                <a href="{{ $reference_path }}">Complete Reference Form</a>
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