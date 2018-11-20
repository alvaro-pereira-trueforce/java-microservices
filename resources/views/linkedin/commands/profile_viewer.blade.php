<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <link href="{{ asset('custom_css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/font-awesome.css') }}" rel="stylesheet">
</head>
<body>
<h4 style=" text-align: center">{{$message}}</h4>
<div class="card center" style="max-width: 60%">
    @foreach ($listProfiles as $profiles)
        <div class="card">
            <div class="card-body">
                <a href="{{$profiles['siteStandardProfileRequest']}}" class="card-link"><span>{{$profiles['firstName'].' '.$profiles['lastName'] }}</span></a>
                <p class="card-text">
                    <small class="text-muted">"{{$profiles['headline']}}"</small>
                </p>
            </div>
        </div>
    @endforeach
</div>
</body>
</html>