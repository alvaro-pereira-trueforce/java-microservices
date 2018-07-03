<html id="adminpage">
<head>
    <link href="{{ asset('custom_css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/docs.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/bootstrap-social.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/bootstrap-override.css') }}" rel="stylesheet">
    <script src="{{ asset('custom_js/jquery.js') }}"></script>
    <script src="{{ asset('custom_js/docs.js') }}"></script>
    <script src="{{ asset('custom_js/utilities.js') }}"></script>
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
        <p>For more information about how to obtain the Telegram token use the following link <a
                    href="https://telegram.me/botfather" target="_blank"> Telegram Bot Father</a> or visit our <a
                    href="{{env('APP_URL')}}/app/#/telegram" target="_blank">Getting Started</a> Page.<br><br>
        </p>

        <form method="post" action="{{$submitURL}}">
            @if($errors)
                @foreach ($errors as $error)
                    <div class="form-group">
                        <div class="alert alert-danger" role="alert">
                            {{$error}}
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="form-group">
                <label>Integration Name:</label>
                <input type="text" name="name" class="form-control" value="{{$name}}" readonly>
            </div>

            <div class="form-group">
                <label>Telegram Token:</label>
                <input type="text" name="token" class="form-control" value="{{$token}}" readonly>
            </div>

            <input type="hidden"
                   name="return_url" value="{{$return_url}}">
            <input type="hidden"
                   name="subdomain" value="{{$subdomain}}">
            <input type="hidden"
                   name="submitURL" value="{{$submitURL}}">
            <input type="hidden"
                   name="telegram_mode_without_settings" value="mode">
            <div class="form-group">
                <input type="submit" class="btn btn-primary">
            </div>
        </form>
    </div>
</div>
<script>
    var hasHelloMessage = '{{$has_hello_message}}';
    if (hasHelloMessage) {
        showElement('message');
    }

    function checkBox(object) {
        if (object.checked) {
            showElement('message');
            return;
        }
        hideElement('message');
    }
</script>
</body>
</html>