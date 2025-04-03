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
            <h1 class="title">Hello {{ $candidate_name }}</h1>
            <p>You have a new document access request from <strong>{{ $client_name }}</strong>. Please click on the link below to accept or reject the access request:</p>
            <div class="button">
                <a href="{{ $dashboard_path }}">Click here to review the request</a>
            </div>
            <p>You can also view the document access request by navigating to <strong>Documents -> Document Access Requests</strong> in your account.</p>
            <br><br>
            <p>
                Thank you,<br>
                {{ config('app.name') }}
            </p>
        </div>
    </div>

</body>

</html>