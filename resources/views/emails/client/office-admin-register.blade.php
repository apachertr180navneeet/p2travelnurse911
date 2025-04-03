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
            <h1 class="title">Welcome, {{ $office_admin_name }}</h1>
            <p>You have been added as an Office Admin at {{ config('app.name') }} for the <?php echo $company_label; ?> <strong><?php echo $company_name; ?></strong>. Below are your login credentials:</p>
            <br />
            <p><strong>Email</strong>: <?php echo $office_admin_email; ?></p>
            <p><strong>Password</strong>: <?php echo $temporary_password; ?></p>
            <br />
            <p>To access your dashboard and begin managing your tasks, please use the following link:</p>
            <div class="button">
                <a href="{{ $office_admin_login_url }}">Login Now</a>
            </div>
            <br />
            <br />
            <p>Thank you,<br>{{ config('app.name') }}</p>
        </div>

    </div>

</body>

</html>