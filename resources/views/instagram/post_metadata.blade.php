<html>
<body>
<form id="finish"
      method="post"
      action="{{$return_url}}">
    <input type="hidden"
           name="name"
           value="{{$name}}">
    <input type="hidden"
           name="metadata"
           value="{{$metadata}}">
    <input type="hidden"
           name="state"
           value="{{$state}}">
</form>
<script type="text/javascript">
    // Post the form
    var form = document.forms['finish'];
    form.submit();
</script>
</body>
</html>