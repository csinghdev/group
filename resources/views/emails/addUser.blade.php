<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h3>{{ $username }} has invited you to join {{$group_name}} on MyGroup</h3>
<br>
<div>
    Please follow the link below to join <h4>{{$group_name}}</h4> on MyGroup
    {{ URL::to('register/'. $email . '/verify/' . $confirmation_code) }}.<br/>
</div>

</body>
</html>