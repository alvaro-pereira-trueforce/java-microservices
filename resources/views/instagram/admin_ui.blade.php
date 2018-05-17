<html id="adminpage">
<head>
    <!-- Latest compiled and minified CSS -->
    <link href="{{ asset('custom_css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/docs.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/bootstrap-social.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/bootstrap-override.css') }}" rel="stylesheet">
    <script src="{{ asset('custom_js/jquery.js') }}"></script>
    <script src="{{ asset('custom_js/docs.js') }}"></script>
</head>
<body>
<div class="panel panel-default center" style="max-width: 800px">
    <div class="panel-heading">
        <h3 class="panel-title">Instagram configuration</h3>
    </div>
    <div class="panel-body">
        <form id="form_setup" method="post" action="{{$submitURL}}">
            <div class="form-group">
                <label>Integration Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="{{$name}}">
            </div>
            <input type="hidden"
                   name="return_url" value="{{$return_url}}">
            <input type="hidden"
                   name="subdomain" value="{{$subdomain}}">
            <input type="hidden"
                   name="submitURL" value="{{$submitURL}}">
            <input id="token" type="hidden"
                   name="token">
            @if($errors)
                @foreach ($errors as $error)
                    <div id="error" class="alert alert-danger" role="alert">
                        {{$error}}
                    </div>
                @endforeach
            @endif
            <div id="error" class="alert alert-danger hide" role="alert"></div>
        </form>

        <div id="spinner" class="loader center hide m-b-10"></div>
        <a class="btn btn-block btn-social btn-facebook center" style="max-width: 200px"
           onClick="logInWithFacebook()">
            <span class="fa fa-facebook"></span> Log in with Facebook
        </a>
    </div>
</div>

<script>

    $('#form_setup').on('keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    logInWithFacebook = function () {
        hideError();
        showSpinner();
        var form = document.forms['form_setup'];
        var text = document.getElementById('name').value;
        if (!text) {
            showError('The integration name is required.');
            hideSpinner();
            return;
        }

        FB.login(function (response) {
            hideSpinner();
            if (response.authResponse) {
                document.getElementById("token").value = document.cookie;
                form.submit();
            } else {
                showError('Login with facebook was cancel, try again.');
            }
        });
        return false;
    };

    function showSpinner() {
        var spinner = document.getElementById("spinner");
        spinner.classList.remove('hide');
    }

    function hideSpinner() {
        var spinner = document.getElementById("spinner");
        spinner.classList.add('hide');
    }

    function hideError() {
        var errorField = document.getElementById("error");
        errorField.classList.add('hide');
    }

    function showError(error) {
        var errorField = document.getElementById("error");
        errorField.innerHTML = error;
        errorField.classList.remove('hide');
        document.cookie = "";
    }

    window.fbAsyncInit = function () {
        FB.init({
            appId: '{{$app_id}}',
            cookie: true, // This is important, it's not enabled by default
            version: '{{$graph_version}}'
        });
    };

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

</body>
</html>