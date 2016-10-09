<!DOCTYPE html>
<html>
<head>
    <title>{{ $Think['const']['APP_NAME'] }}@lang('common.management')@lang('common.backend')</title>
    <link href="__ROOT__/Public/css/bimages/favicon.ico" type="image/ico" rel="shortcut icon"/>
    <meta http-equiv="Content-Type" Content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap#min.css') }}"/>
    <!--[if lt IE 10]>
    <script type="text/javascript" src="{{ asset('js/supporthtml5.js') }}"></script>
    <![endif]-->
</head>
<frameset rows="50,*" framespacing="0" border="0">
    <frame id="top_nav" name="top_nav" src="{{ route('Index/top_nav') }}" frameborder="no" scrolling="no"/>
    <frameset rows="*" cols="220,*" framespacing="0" border="0">
        <frame id="left_nav" name="left_nav" src="{{ route('Index/left_nav',array('menu_type'=>$default_menu_type)) }}"
               frameborder="no" scrolling="yes"/>
        <frame id="main" name="main" src="{{ route('Index/main') }}" frameborder="no" scrolling="yes"/>
    </frameset>
</frameset>
</html>