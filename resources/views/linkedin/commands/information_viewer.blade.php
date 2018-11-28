<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
</head>
<body>
<h4 style=" text-align: center">{{$message}}</h4>
    <div class="card" style="max-width: 800px">
        <div class="card-body">
            <h5 class="card-title"> {{$information['name']}}</h5>
            <img class="card-img-top" src="{{$information['logo-url']}}" style="max-width: 60%">
            <p class="card-text"><b> Description: </b>{{$information['description']}}</p>
            <p class="card-text"><b>Company Type:</b>{{$information['company_type']}}</p>
            <p class="card-text"><b>Employees: </b>{{$information['employeeCountRange']}}</p>
            <p class="card-text"><b>Industry: </b>{{$information['industries']}}</p>
            <p class="card-text"><b>City: </b>{{$information['locations']['city']}}</p>
            <p class="card-text"><b>PostCode: </b>{{$information['locations']['postalCode']}}</p>
            <p class="card-text"><b>Street: </b>{{$information['locations']['street']}}</p>
            <p class="card-text"><b>Specialities: </b>{{$information['specialties']}}</p>
            <p class="card-text"><b>Website Url: </b>{{$information['website_url']}}</p>
        </div>
    </div>
</body>
</html>