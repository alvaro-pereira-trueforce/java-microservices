<html id="adminpage">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style>
        .center {
            margin: auto;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="card center" style="max-width: 800px">
    <div class="card-body">
        <h3>Telegram configuration</h3>
        @if (!$name)
            <p>We need you to provide us your Telegram token which was generated with the "Telegram Bot Father" in order
                to start working with the Telegram Channel service.</p>
        @else
            <p>Welcome back. Do you want to update the current telegram token?, Just provide us
                the telegram token that was generated with Telegram Bot Father in order to start
                working with Telegram Channel Service.</p>
        @endif
        <p>For more information about how to obtain the Telegram token use the following link: </p>
        <a href="https://telegram.me/botfather" target="_blank"> Telegram Bot Father</a><br><br>
        <form method="post" action="{{$submitURL}}">
            <div class="form-group">
                <label>Integration Name:</label>
                <input type="text" name="name" class="form-control" value="{{$name}}">
            </div>
            <div class="form-group">
                <label>Telegram Token:</label>
                <input type="text" name="token" class="form-control" value="{{$token}}">
            </div>
            <input type="hidden"
                   name="return_url" value="{{$return_url}}">
            <input type="hidden"
                   name="subdomain" value="{{$subdomain}}">
            <input type="hidden"
                   name="submitURL" value="{{$submitURL}}">
            @if($errors)
                @foreach ($errors as $error)
                    <div class="alert alert-danger" role="alert">
                        {{$error}}
                    </div>
                @endforeach
            @endif
            <input type="submit" class="btn btn-primary">
        </form>
    </div>
</div>
</body>
</html>