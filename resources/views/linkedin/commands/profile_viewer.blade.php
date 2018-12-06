<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <style>
        .center {
            margin: auto;
            width: 100%;
        }
    </style>
</head>
<body>
<h4 style=" text-align: center">{{$message}}</h4>
    @foreach ($listProfiles as $profiles)
        <div class="card center" style="max-width: 800px">
            <div class="card-body">
                <a href="{{$profiles['siteStandardProfileRequest']}}" class="card-link"><span>{{$profiles['firstName'].' '.$profiles['lastName'] }}</span></a>
                <p class="card-text">
                    <small class="text-muted">"{{$profiles['headline']}}"</small>
                </p>
            </div>
        </div>
    @endforeach
</body>
</html>