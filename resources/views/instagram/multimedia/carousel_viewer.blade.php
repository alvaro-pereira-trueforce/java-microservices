<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<body>
<p>{{$message}}</p>
<div style="display: flex; flex-flow: wrap;">
    @foreach ($children as $child)
        @if (array_key_exists('media_type', $child) && array_key_exists('media_url', $child) && $child['media_type'] == 'IMAGE')
            <div style="display: flex; flex-direction: column; width: 150px; min-width: 150px; margin-right: 20px; margin-top: 20px;">
                <img src="{{$child['media_url']}}" style="width: 100%">
            </div>
        @elseif (array_key_exists('media_type', $child) && array_key_exists('media_url', $child) && array_key_exists('thumbnail_url', $child) && $child['media_type'] == 'VIDEO')
            <div style="display: flex; flex-direction: column; width: 150px; min-width: 150px; margin-right: 20px; margin-top: 20px;">
                <img src="{{$child['thumbnail_url']}}" style="width: 100%">
                <p><a href="{{$child['media_url']}}">See video in Instagram</a></p>
            </div>
        @endif
    @endforeach
</div>
</body>
</html>