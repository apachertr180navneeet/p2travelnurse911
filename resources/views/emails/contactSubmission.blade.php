<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $title }} || {{ config('app.name') }}</title>
	@include('emails.style')
</head>

<body>
	<div id="box">
		<div class="text">
			<h1 class="title">Hello Admin</h1>
			<p>{{ $description }}</p><br>
			<p>Name : {{ $name ?? '-'}}</p>
			<p>Email : {{ $email ?? '-'}}</p>
			<p>Subject : {{ $subject ?? '-'}}</p>
			<p>Message : {{ $usermessage ?? '-' }}</p>


			<br><br>
			<p>
				Thank you,<br>
				{{ config('app.name') }}
			</p>
		</div>
	</div>

</body>

</html>