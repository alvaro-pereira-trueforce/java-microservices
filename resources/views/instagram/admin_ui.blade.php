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

        @if (!$name)
            <p>Welcome, please authenticate first with your Facebook Account in order to start working with the Instagram Channel service.</p>
        @else
            <p>Welcome back.</p>
        @endif

        <form id="form_setup" method="post" action="{{$submitURL.'admin_ui_2'}}">
            <div class="form-group">
                <label>Integration Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="{{$name}}">
            </div>
            <input type="hidden" id="return_url"
                   name="return_url" value="{{$return_url}}">
            <input type="hidden" id="subdomain"
                   name="subdomain" value="{{$subdomain}}">
            <input type="hidden" id="uuid"
                   name="uuid">
            <div class="form-group">
                @if($errors)
                    @foreach ($errors as $error)
                        <div class="alert alert-danger" role="alert">
                            {{$error}}
                        </div>
                    @endforeach
                @endif
                <div id="error" class="alert alert-danger hide" role="alert"></div>
            </div>
        </form>

        <div id="spinner" class="loader center hide m-b-10"></div>

        <a class="btn btn-block btn-social btn-facebook center" style="max-width: 200px"
           target="_blank"
           onclick="return startFacebookProcess()"
        >
            <span class="fa fa-facebook"></span>Connect To Facebook
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

    function enableItem(id) {
        var item = document.getElementById(id);
        item.readOnly = false;
    }

    function disableItem(id) {
        var item = document.getElementById(id);
        item.readOnly = true;
    }

    var waiting = false;
    var count = 0;

    function startFacebookProcess() {
        var name = document.getElementById('name').value;
        var subdomain = document.getElementById('subdomain').value;

        if (!name) {
            showError('The integration name is required (Ex: Business Clients Support).');
            return;
        }

        if (waiting) {
            return;
        }

        disableItem('name');
        hideError();
        showSpinner();
        waiting = true;
        count = 0;
        create_facebook_registration(name, subdomain);
    }
    
    function create_facebook_registration(integration_name, subdomain) {
        $.post('{{$submitURL.'admin_create_facebook_registration'}}', {
            name: integration_name,
            subdomain: subdomain
        }).done(function (response) {
            var url = "https://www.facebook.com/v3" +
                ".0/dialog/oauth?client_id={{$app_id}}&redirect_uri={{{$submitURL
            .'admin_facebook_auth'}}}&state={\"uuid\":\""+response+"\"}"+
            '&scope=manage_pages,pages_show_list,instagram_basic,instagram_manage_insights,instagram_manage_comments';
            window.open(url);
            timeout(response)
        })
    }
    
    function timeout(uuid) {
        $.post('{{$submitURL.'admin_wait_facebook'}}', {
            uuid: uuid
        }).done(function (response) {
            document.getElementById('uuid').value = response;
            $('#form_setup').submit();
            hideSpinner();
            waiting = false;
        }).fail(function (xhr, status, message) {
            count++;
            if (count > 20) {
                enableItem('name');
                waiting = false;
                hideSpinner();
                showError('The facebook authentication timeout was reached. Please, Try again.');
                return;
            }
            setTimeout(function () {
                timeout(uuid);
            }, 1000);
        });
    }

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
</script>

</body>
</html>