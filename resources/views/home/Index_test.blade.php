<!DOCTYPE html>
<html>
<head>
    <title>@if (isset($title)){{ $title }} @lang('common.dash')@endif {{ config('website.site_title') }}</title>
    <link href="{{ asset('css/bimages/favicon.ico') }}" type="image/ico" rel="shortcut icon"/>
    <meta http-equiv="Content-Type" Content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (config('system.site_keywords'))
        <meta name="keywords" content="{{ config('website.site_keywords') }}"/>@endif
    @if (config('system.site_keywords'))
        <meta name="description" content="{{ config('website.site_description') }}"/>@endif
    <meta name="author" content="{{ trans('common.pfcopyright',['app_name'=>trans('common.app_name')]) }}"/>

    <link rel="stylesheet"
          href="{{ route('Minify',['type'=>'css','files'=>'jquery-ui.min,bootstrap.min,bootstrap-theme.min,common,home']) }}">
    <script type="text/javascript"
            src="{{ route('Minify',['type'=>'js','files'=>'jquery.min,bootstrap.min,jquery-ui.min,common','lang'=>'common,frontend']) }}"></script>
    <!--[if lt IE 10]>
    <script type="text/javascript" src="{{ route('Minify',['type'=>'js','files'=>'supporthtml5']) }}"></script>
    <![endif]-->
    @stack('csses')
</head>
<body>
<section class="container">
    <ul class="list-group">
        <li class="list-group-item"><a href="{{ route('Home::Member::index') }}">@lang('common.member')</a></li>
        <li class="list-group-item"><a href="{{ route('Home::Quests::index') }}">问卷</a></li>
        <li class="list-group-item"><a href="{{ route('Home::Assess::index') }}">考核</a></li>
        <li class="list-group-item"><a href="{{ route('Home::MessageBoard::index') }}">留言板</a></li>
    </ul>
</section>
<section class="container">
    <div class="row">
        <div id="test_region" class="col-sm-12">

        </div>
        <script type="text/javascript" src="{{ asset('js/M_multilevel_selection.js') }}"></script>
        <script type="text/javascript">
            var M_multilevel_selection_obj;
            $(function () {
                var config = {
                    'out_obj': $('#test_region'),
                    'post_name': 'region_id',
                    'ajax_url': '{{ route('Home::Region::ajax_api') }}'
                };
                M_multilevel_selection_obj = new M_multilevel_selection(config);
            });
        </script>
    </div>
</section>
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