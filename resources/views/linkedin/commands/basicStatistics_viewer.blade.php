<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
</head>
<body>
<h4 style=" text-align: center">{{$message}}</h4>
<div class="card" style="max-width: 800px">
    <div class="card-body">
        <p class="card-text"><b>Employees: </b>{{$information['employees']}}</p>
        <p class="card-text"><b>Followers: </b>{{$information['followers']}}</p>
        <p class="card-text"><b>Viewers and others: </b>{{$information['viewers']}}</p>
        <p class="card-text"><b>Clicks: </b>{{$information['clicks']}}</p>
        <p class="card-text"><b>Comments: </b>{{$information['comments']}}</p>
        <p class="card-text"><b>Impressions: </b>{{$information['impressions']}}</p>
        <p class="card-text"><b>likes: </b>{{$information['likes']}}</p>
        <p class="card-text"><b>Shares: </b>{{$information['shares']}}</p>
    </div>
</div>
</body>
</html>