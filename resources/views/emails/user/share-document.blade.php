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
            <h1 class="title">Hello</h1>
            <p>Welcome to {{ config('app.name') }}</p>
            <p>{{ ucfirst($full_name) }} shared a document with you via {{ config('app.name') }}.</p>
            <div class="button">
                <a href="{{ $doc_path }}" download>Download</a>
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
