<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h4>Hi {{ $username }}</h4>
<br>
<div>
    Thanks for creating an account on MyGroup.<br>
    Please follow the link below to verify your email address
    {{ URL::to('api/v1/register/'. $email . '/verify/' . $confirmation_code) }}.<br/>
</div>

</body>
</html>