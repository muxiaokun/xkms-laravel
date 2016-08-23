@include('Public:header')
        <header class="top_nav h50">
            <div class="title"></div>
            <div class="companytitle pt20 pl30 text-left fs12">{{ config('SITE_COMPANY') }}</div>
            <div class="right_menu">
                <a class="btn btn-sm btn-primary" href="{{ route('Admin/Index/logout') }}" target="_parent">{{ trans('common.logout') }}&nbsp;[{{ $Think['session']['backend_info']['admin_name'] }}]</a>
            </div>
            <div class="right_menu">
                <a class="btn btn-sm btn-primary" href="{{ route('Admin/Index/edit_my_pass') }}" target="main">{{ trans('common.edit') }}&nbsp;[{{ $Think['session']['backend_info']['admin_name']}]&nbsp;{{ trans('common.pass') } }}</a>
            </div>
            <div class="right_menu">
                <a class="btn btn-sm btn-primary" href="{{ route('Admin/Index/main') }}" target="main">{{ trans('common.backend') }}{{ trans('common.homepage') }}</a>
            </div>
            <div class="right_menu">
                <a class="btn btn-sm btn-primary" href="__ROOT__/" target="_blank">{{ trans('common.frontend') }}{{ trans('common.homepage') }}</a>
            </div>
        </header>
    </body>
</html>