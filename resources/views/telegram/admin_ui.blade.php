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
            <p>Welcome back. Do you want to update the configuration?.</p>
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
            @if($token_hide)
                <div class="form-group">
                    <label>Integration Name:</label>
                    <input type="text" name="name" class="form-control" value="{{$name}}" readonly>
                </div>

                <div class="form-group">
                    <label>Telegram Token:</label>
                    <input type="text" name="token" class="form-control" value="{{$token}}" readonly>
                </div>
            @else
                <div class="form-group">
                    <label>Integration Name:</label>
                    <input type="text" name="name" class="form-control" value="{{$name}}">
                </div>
                <div class="form-group">
                    <label>Telegram Token:</label>
                    <input type="text" name="token" class="form-control" value="{{$token}}">
                </div>
            @endif

            <div class="checkbox">
                <label>
                    @if($has_hello_message)
                        <input type="checkbox" name="has_hello_message" onclick="checkBox(this)" checked>Enable Hello
                        Message?
                    @else
                        <input type="checkbox" name="has_hello_message" onclick="checkBox(this)">Enable Hello Message?
                    @endif
                </label>
            </div>

            <div id="message" class="form-group hide">
                <label>Custom Message:</label>
                <input type="text" name="hello_message" class="form-control" value="{{$hello_message}}">
                <div class="text-muted">Telegram bot will sent this message on start command.</div>
            </div>

            <div class="checkbox">
                <label>
                    @if($required_user_info)
                        <input type="checkbox" name="required_user_info" checked>Ask basic information?
                    @else
                        <input type="checkbox" name="required_user_info">Ask basic information?
                    @endif
                </label>
                <div class="text-muted">Telegram bot will ask the user's email address and phone number on start
                    command.
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <h4>Optional Settings</h4>
                <p>Choose the optional ticket field values, all the values are optionals.
                    Please consider that all your new telegram tickets will be created with the optional settings you choose.</p>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Ticket Type:</label>
                        {{Form::select('ticket_type', array('' => '-','problem' => 'Problem','incident' => 'Incident','question' =>'Question','task' => 'Task'),$ticket_type,['class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Ticket Priority:</label>
                        {{Form::select('ticket_priority', array('' => '-','urgent' => 'Urgent','high' => 'High','normal' =>'Normal','low' =>'Low'),$ticket_priority,['class' => 'form-control'])}}
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label>Tags:</label>
                <input type="text" name="tags" class="form-control" value="{{$tags}}">
                <div class="text-muted">Please separate tags with spaces.</div>
            </div>

            <input type="hidden"
                   name="return_url" value="{{$return_url}}">
            <input type="hidden"
                   name="subdomain" value="{{$subdomain}}">
            <input type="hidden"
                   name="submitURL" value="{{$submitURL}}">

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