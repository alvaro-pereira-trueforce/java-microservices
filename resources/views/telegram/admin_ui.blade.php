<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
            <p>We need to you provide us the telegram token that was generated with Telegram Bot Father in order to start working with telegram channel service.</p>
        @else
            <p>Welcome back {{$name}}. Do you want to update the current telegram token?, Just provide us the telegram token that was generated with Telegram Bot Father in order to start working with telegram channel service.</p>
        @endif
        <p>For more information that how to obtain the telegram token use the following link: </p>
        <a href="https://telegram.me/botfather"> Telegram Bot Father</a><br><br>
        <form method="post" action="{{$submitURL}}">
            <div class="form-group">
                <label>Telegram Token:</label>
                <input type="text" name="token" class="form-control ">
            </div>
            <input type="hidden"
                   name="return_url" value="{{$return_url}}">
            <input type="hidden"
                   name="subdomain" value="{{$subdomain}}">
            <input type="hidden"
                   name="name" value="{{$name}}">
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