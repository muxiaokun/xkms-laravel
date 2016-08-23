<!DOCTYPE html>
<html>
    <head>
        <title>@if ($title){{ $title }} {{ trans('common.dash') }}@endif {$Think.APP_NAME}{{ trans('common.management') }}{{ trans('common.backend') }}</title>
        <link href="__ROOT__/Public/css/bimages/favicon.ico" type="image/ico" rel="shortcut icon" />
        <meta http-equiv="Content-Type" Content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="{{ asset('css/jquery-ui#min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/jquery-ui#theme#min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/bootstrap#min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/bootstrap-theme#min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
        <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/jquery-ui.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
        <!--[if lt IE 10]>
            <script type="text/javascript" src="{{ asset('js/supporthtml5.js') }}"></script>
        <![endif]-->
    </head>
    <body>
