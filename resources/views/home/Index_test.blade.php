<!DOCTYPE html>
<html>
<head>
    <title>@if ($title){{ $title }} @lang('common.dash')@endif {{ config('website.site_title') }}</title>
    <link href="{{ asset('css/bimages/favicon.ico') }}" type="image/ico" rel="shortcut icon"/>
    <meta http-equiv="Content-Type" Content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="keywords" content="{{ config('website.site_keywords') }}"/>
    <meta name="description" content="{{ config('website.site_description') }}"/>
    <meta name="author" content="{:L('pfcopyright',array('app_name'=>APP_NAME))}"/>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui#min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap#min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-theme#min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
    <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
    <!--[if lt IE 10]>
    <script type="text/javascript" src="{{ asset('js/supporthtml5.js') }}"></script>
    <![endif]-->
    <!--[if lt IE 10]>
    <script type="text/javascript" src="{{ asset('js/supporthtml5.js') }}"></script><![endif]-->
</head>
<body>
<section class="container">
    <div class="w1000 h400">
        @asyncImg(<img src="{{ mExists('Uploads/attached/image/index/banner1.png') }}"/>)
    </div>
    <div class="w1000 h400">
        @asyncImg(<img src="Uploads/attached/image/index/banner2.png"/>)
    </div>
    <div class="w1000 h400">
        @asyncImg(<img src="Uploads/attached/image/index/banner1.png"/>)
    </div>
    <div class="w1000 h400">
        @asyncImg(<img src="Uploads/attached/image/index/banner2.png"/>)
    </div>
</section>
<section class="container hidden">
    <div class="jumbotron">
        <h1>{{ $Think['const']['APP_NAME'] }}</h1>
        <p>@lang('common.version')@lang('common.colon') Home Module 1.0.0</p>
        <p><a class="btn btn-primary btn-lg" role="button" href="http://www.xjhywh.cn" target="_blank">Learn more</a>
        </p>
    </div>
    <ul class="list-group">
        <li class="list-group-item"><a href="{:M_U('Member/index')}">@lang('common.member')</a></li>
        <li class="list-group-item"><a href="{:M_U('Quests/index')}">问卷</a></li>
        <li class="list-group-item"><a href="{:M_U('Assess/index')}">考核</a></li>
        <li class="list-group-item"><a href="{:M_U('MessageBoard/index')}">留言板</a></li>
    </ul>
</section>
</body>
</html>