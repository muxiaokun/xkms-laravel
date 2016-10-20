<!DOCTYPE html>
<html>
<head>
    <title>@lang('common.system')@lang('common.info') @lang('common.dash') {{ config('website.site_title') }}</title>
    <link href="__ROOT__/Public/css/bimages/favicon.ico" type="image/ico" rel="shortcut icon"/>
    <meta http-equiv="Content-Type" Content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet"
          href="{{ route('Minify',['type'=>'css','files'=>'jquery-ui.min,jquery-ui.theme.min,jquery-ui.structure.min,bootstrap.min,bootstrap-theme.min,common,admin']) }}">
    <script type="text/javascript"
            src="{{ route('Minify',['type'=>'js','files'=>'jquery.min,jquery-ui.min,common','lang'=>'common,admin']) }}"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('js/supporthtml5.js') }}"></script>
    <![endif]-->
</head>
<body>
<script type="text/javascript">
    (function () {
        M_confirm('{{ $lang }}?', '{{ $backUrl }}', true);
    })();
</script>
</body>
</html>