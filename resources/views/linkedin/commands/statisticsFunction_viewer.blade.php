<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
</head>
<body>
<h4 style=" text-align: center">{{$message}}</h4>
<div class="card" style="max-width: 60%">
    <div class="card-body">
        <ul>
            @foreach ($informationFunctions as $function)
                <li><p class="card-text">{{$function['function']}}</p></li>
            @endforeach
        </ul>
    </div>
</div>
<p class="card-text">
    <small class="text-muted">"Note: All the information showed are the company's functions"</small>
</p>
</body>
</html>