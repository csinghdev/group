<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h3>{{ $username }} has invited you to join {{$group_name}} on MyGroup</h3>
<br>
<div>
    To join <h4>{{$group_name}}</h4> on MyGroup.<br>
    Register using these details :<br>
    Email id : {{$email}} <br>
    Unique Code : {{ $confirmation_code }}
</div>

</body>
</html>