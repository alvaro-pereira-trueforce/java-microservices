<html id="adminpage">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
            <p>We need you to provide us your Telegram token which was generated with the "Telegram Bot Father" in order to start working with the Telegram Channel service.</p>
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
                <input type="text" name="token" class="form-control">
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
        <div>
            <h4>Current configuration</h4>

            @if(count($current_accounts) == 0)
                <label>No records saved.</label>
            @endif
            @foreach ($current_accounts as $account)
                <div class="card" style="margin-top: 10px">
                    <div class="card-body">
                        <h5 class="card-title">{{$account->integration_name}}</h5>
                        <p class="card-text">{{$account->token}}</p>
                        <button class="btn btn-secondary" style="margin-right: 10px"
                                onclick="addClick({{$account}},'{{$return_url}}',
                                        '{{$submitURL}}')">
                            Add to account
                        </button>
                        <button class="btn btn-danger"
                        onclick="removeClick('{{$account->uuid}}', '{{$return_url}}',
                                '{{$subdomain}}','{{$name}}', '{{$submitURL}}')">Remove
                        permanently</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
</body>
<script>
    function addClick(account, return_url, submitURL) {
        var request_url = submitURL + '/' + account.uuid;
        $.post(request_url,
            {
                account: account,
                return_url: return_url
            },
            function (data, status) {
                console.log(status);
                if(status === 'success')
                {
                    $("body").html(data);
                }
            });
    }

    function removeClick(uuid, return_url, subdomain, name, submitURL) {
        var request_url = submitURL + '/delete/' + uuid;
        var body = {
            return_url: return_url,
            subdomain: subdomain,
            name: name,
            submitURL: submitURL
        };

        $.post(request_url,
            body,
            function (data, status) {
                if(status === 'success')
                {
                    $("body").html(data);
                }
            });
    }
</script>
</html>