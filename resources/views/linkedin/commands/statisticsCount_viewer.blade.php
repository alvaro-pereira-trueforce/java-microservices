<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
</head>
<body>
<h4 style=" text-align: center">{{$message}}</h4>
<div class="card" style="max-width: 800px">
    <div class="card-body">
        @foreach ($informationCounts as $counts)
            <p class="card-text"><b>{{$counts['month'] . ' - '. $counts['year'].':'}} </b>{{$counts['newCount']}}</p>
        @endforeach
    </div>
</div>
<p class="card-text">
    <small class="text-muted">"Note: All the information showed belongs to the last 12 month of statistics"</small>
</p>
</body>
</html>