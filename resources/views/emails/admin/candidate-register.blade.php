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
            <h1 class="title">Hello {{ $admin_name }}</h1>
            <p>A new candidate has been registered at {{ config('app.name') }}. Below are the details of the candidate:</p>
            <br>
            <p><strong>Full Name</strong>: <?php echo $full_name; ?></p>
            <p><strong>Email</strong>: <?php echo $email; ?></p>
            <br />
            <p>To access the candidate information and manage the registration, please use the following link:</p>
            <div class="button">
                <a href="{{ $admin_login_url }}">Login to Admin Dashboard</a>
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