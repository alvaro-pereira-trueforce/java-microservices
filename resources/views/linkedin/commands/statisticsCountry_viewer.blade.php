<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
</head>
<body>
<h4 style=" text-align: center">{{$message}}</h4>
<div class="card" style="max-width: 800px">
    <div class="card-body">
        <ul>
            @foreach ($informationCountries as $country)
                <li><p class="card-text">{{$country['country']}}</p></li>
            @endforeach
        </ul>
    </div>
</div>
<p class="card-text">
    <small class="text-muted">"Note: all the countries showed are related to the company employees and followers"
    </small>
</p>
</body>
</html>