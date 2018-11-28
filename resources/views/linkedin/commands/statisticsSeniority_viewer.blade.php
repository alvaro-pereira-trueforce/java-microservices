<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
</head>
<body>
<h4 style=" text-align: center">{{$message}}</h4>
<div class="card" style="max-width: 800px">
    <div class="card-body">
        <ul>
            @foreach ($informationSeniorities as $seniority)
                <li><p class="card-text">{{$seniority['seniority']}}</p></li>
            @endforeach
        </ul>
    </div>
</div>
<p class="card-text">
    <small class="text-muted">"Note: All the information showed belongs to our employees specializations"</small>
</p>
</body>
</html>