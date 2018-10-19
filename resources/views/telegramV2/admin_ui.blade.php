@extends('admin_ui_template')

@section('styles')
    <link rel="stylesheet/less" type="text/css" href="{{ asset('modules/telegram/admin_ui/admin_ui.less') }}">
@stop
@section('content')
    <script src="{{ asset('modules/telegram/admin_ui/admin_ui.js') }}"></script>
    <div ng-app="adminUI.telegram">
        <div ng-view></div>
    </div>
@stop