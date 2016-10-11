<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }} @lang('common.dash') @lang('common.app_name')</title>
    <link href="__ROOT__/Public/css/bimages/favicon.ico" type="image/ico" rel="shortcut icon"/>
    <meta http-equiv="Content-Type" Content="text/html;charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet"
          href="{{ route('Minify',['type'=>'css','files'=>'common,bootstrap.min,bootstrap-theme.min,install']) }}">
    <script type="text/javascript"
            src="{{ route('Minify',['type'=>'js','files'=>'jquery.min,bootstrap.min,install']) }}"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ route('Minify',['type'=>'js','files'=>'supporthtml5']) }}"></script>
    <![endif]-->
    @stack('csses')
</head>
<body>
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
@stack('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
</body>
</html>