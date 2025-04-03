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
			<p>Name : {{ $name ?? '-'}}</p><br>
			<p>Email : {{ $email ?? '-'}}</p><br>
			<p>Phone : {{ $phone ?? '-'}}</p><br>
			<p>Company Name : {{ $company_name ?? '-' }}</p><br>
			<p>Type Of Company : {{ $type_of_company ?? '-' }}</p><br>
			<p>Other : {{ $other_type ?? '-' }}</p>

			<br><br>
			<p>
				Thank you,<br>
				{{ config('app.name') }}
			</p>
		</div>
	</div>

</body>

</html>