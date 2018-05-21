<html id="adminpage">
<head>
    <link href="{{ asset('custom_css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/docs.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/bootstrap-social.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/bootstrap-override.css') }}" rel="stylesheet">
    <script src="{{ asset('custom_js/jquery.js') }}"></script>
    <script src="{{ asset('custom_js/bootstrap.js') }}"></script>
    <script src="{{ asset('custom_js/docs.js') }}"></script>
</head>
<body>
<div class="panel panel-default center" style="max-width: 800px">
    <div class="panel-heading">
        <h3 class="panel-title">Instagram configuration</h3>
    </div>
    <div class="panel-body">
        <form id="form_setup" method="post" action="{{$submitURL.'admin_ui_submit'}}">
            <div class="form-group">
                <label>Integration Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="{{$name}}">
            </div>
            <input type="hidden"
                   name="return_url" value="{{$return_url}}">
            <input type="hidden"
                   name="subdomain" value="{{$subdomain}}">
            <input type="hidden"
                   name="submitURL" value="{{$submitURL.'admin_ui_submit'}}">
            <input id="token" type="hidden"
                   name="token" value="{{$accessToken}}">
            <input id="instagram_id" type="hidden"
                   name="instagram_id">
            <div class="form-group">
                <label for="page_id">Select the facebook page with a valid instagram account:</label>
                <select class="form-control" name="page_id" id="page_id">
                    @if($pages)
                        @foreach($pages as $page)
                            <option value="{{$page['id']}}">{{$page['name']}}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group">
                @if($errors)
                    @foreach ($errors as $error)
                        <div id="error" class="alert alert-danger" role="alert">
                            {{$error}}
                        </div>
                    @endforeach
                @endif
                <div id="error" class="alert alert-danger hide" role="alert"></div>
            </div>

            <div class="form-group">
                <button id="send_request" class="btn btn-primary">Submit</button>
            </div>
        </form>
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

    $('#send_request').on('click', function (event) {
        event.preventDefault();
        hideError();
        var text = document.getElementById('name').value;
        if (!text) {
            showError('The integration name is required (Ex: Business Clients Support).');
            return;
        }
        var page_id = document.getElementById('page_id').value;
        var access_token = document.getElementById('token').value;
        $.post('{{$submitURL.'admin_validate_page'}}', {
            page_id: page_id,
            access_token: access_token
        }).done(function (result) {
            document.getElementById('instagram_id').value = result.instagram_id;
            $('#form_setup').submit();
        }).fail(function (xhr, status, message) {
            showError('The page does not have an instagram account, Please use the instagram ' +
                'application to create a facebook page.');
        });
    });

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