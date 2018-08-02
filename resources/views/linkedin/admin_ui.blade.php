@extends('admin_ui_template')

@section('styles')
    <link rel="stylesheet/less" type="text/css" href="{{ asset('modules/linkedin/admin_ui/admin_ui.less') }}">
@stop
@section('content')
    <script src="{{ asset('modules/linkedin/admin_ui/admin_ui.js') }}"></script>
    <div ng-app="adminUI.linkedin">
        <div ng-view></div>
    </div>
@stop