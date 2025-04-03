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
            <h1 class="title">Hi there</h1>
            <div>{!! $description !!}</div> 
            <br><br>
            <p>
                Thank you,<br>
                {{ config('app.name') }}
            </p>
        </div>

    </div>
</body>

</html>