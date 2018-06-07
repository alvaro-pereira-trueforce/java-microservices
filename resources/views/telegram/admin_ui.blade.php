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

        .hide {
            display: none;
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
            <label id="no-records" class="hide">No records saved.</label>
            @foreach ($current_accounts as $account)
                <div id="item-{{$loop->index}}" class="card" style="margin-top: 10px">
                    <div class="card-body">
                        <h5 class="card-title">{{$account->integration_name}}</h5>
                        <p class="card-text">{{$account->token}}</p>
                        <button class="btn btn-secondary" style="margin-right: 10px"
                                onclick="addClick('{{$account->uuid}}','{{$account->integration_name}}','{{$return_url}}', '{{$submitURL}}')">
                            Add to account
                        </button>
                        <button class="btn btn-danger"
                                onclick="removeClick('{{$account->uuid}}','{{$subdomain}}','{{$submitURL}}','item-{{$loop->index}}')">
                            Remove
                            permanently
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
</body>
<script>
    function addClick(uuid, integration_name, return_url, submitURL) {
        var request_url = submitURL + '/' + uuid;
        $.post(request_url,
            {
                uuid: uuid,
                integration_name: integration_name,
                return_url: return_url
            },
            function (data, status) {
                if (status === 'success') {
                    $("body").html(data);
                }
            });
    }

    function removeClick(uuid, subdomain, submitURL, item) {
        var request_url = submitURL + '/delete/' + uuid;
        var body = {
            subdomain: subdomain,
            submitURL: submitURL
        };

        $.post(request_url,
            body,
            function (data, status) {
                if (status === 'success') {
                    if (data.length === 0) {
                        showNoRecords();
                    }
                    var elem = document.getElementById(item);
                    return elem.parentNode.removeChild(elem);
                }
            });
    }

    function showNoRecords() {
        var error = document.getElementById("no-records");
        error.classList.remove('hide');
    }
</script>
</html>