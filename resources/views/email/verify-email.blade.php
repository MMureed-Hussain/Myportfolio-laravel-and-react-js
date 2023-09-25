<!-- {{$user->first_name}}
{{$verificationUrl}} -->
<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Email Verification</h1>

        <p>Dear {{ $user->first_name }},</p>

        <p>Thank you for registering. Please click the button below to verify your email address:</p>

        <a href="{{ $verificationUrl }}" class="btn btn-primary">Verify Email</a>

        <p>If you did not register on our site, you can safely ignore this email.</p>
    </div>

    <!-- Include Bootstrap JS (optional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
