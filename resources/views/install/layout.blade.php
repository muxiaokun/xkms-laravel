<!DOCTYPE html>
<html>
<head>
    <title>{{{ $title }}} @lang('common.dash') @lang('common.app_name')</title>
    <link href="__ROOT__/Public/css/bimages/favicon.ico" type="image/ico" rel="shortcut icon"/>
    <meta http-equiv="Content-Type" Content="text/html;charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-theme.min.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/install.js') }}"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('js/supporthtml5.js') }}"></script>
    <![endif]-->
    @stack('csses')
    @stack('scripts')
</head>
<body>
<style>
    body {
        background-color: #2d527f;
        color: #FFF
    }

    .header_fixed {
        padding: 0 30px;
        width: 100%;
        height: 100px;
        position: fixed;
        z-index: 1;
        background-color: #26384e;
    }

    .header_height {
        height: 150px;
    }

    .footer_fixed {
        width: 100%;
        height: 80px;
        line-height: 30px;
        position: fixed;
        bottom: 0px;
        z-index: 1;
        background-color: #26384e;
        padding: 10px 0;
    }

    .footer_height {
        height: 150px;
    }

    .table-hover > tbody > tr:hover {
        color: #2d527f;
    }

    .g-iframe {
        border: 0 none;
        height: 100%;
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
    }
</style>
@if ($show_height)
    <div class="header_height">
    </div>
@endif
@section('body')
@show
@if ($show_height)
    <div class="footer_height">
    </div>
@endif
</body>
</html>