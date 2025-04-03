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

        #box .text p {
            margin-bottom: 0px;
        }
    </style>
</head>

<body>
    <div id="box">
        <div class="text">
            <h1 class="title">Hello {{ $receiver_name }}</h1>
            <p>You have received a new job application on {{ config('app.name') }}. Below are the details of the application:</p>
            <br>
            <p><strong>Job Title</strong>: <?php echo $job_title; ?></p>
            <p><strong>Applicant Name</strong>: <?php echo $applicant_name; ?></p>
            <p><strong>Applicant Email</strong>: <?php echo $applicant_email; ?></p>
            <br />
            <p>To review the complete application, please use the following link:</p>
            <div class="button">
                <a href="{{ $login_url }}">{{ $login_url_btn_text }}</a>
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