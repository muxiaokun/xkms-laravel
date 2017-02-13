<!DOCTYPE html>
<html>
<head>
    <title>@if ($title){{ $title }} @lang('common.dash')@endif @lang('common.app_name')@lang('common.management')@lang('common.backend')</title>
    <link href="{{ asset('css/bimages/favicon.ico') }}" type="image/ico" rel="shortcut icon"/>
    <meta http-equiv="Content-Type" Content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet"
          href="{{ route('Minify',['type'=>'css','files'=>'jquery-ui.min,jquery-ui.theme.min,bootstrap.min,bootstrap-theme.min,common,admin']) }}">
    <script type="text/javascript"
            src="{{ route('Minify',['type'=>'js','files'=>'jquery.min,jquery-ui.min,common','lang'=>'common,backend']) }}"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ route('Minify',['type'=>'js','files'=>'supporthtml5']) }}"></script>
    <![endif]-->
    @stack('csses')
</head>