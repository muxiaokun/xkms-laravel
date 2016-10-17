@include('admin.Public_header')
<header class="top_nav h50">
    <div class="title"></div>
    <div class="companytitle pt20 pl30 text-left fs12">{{ config('website.site_company') }}</div>
    <div class="right_menu">
        <a class="btn btn-sm btn-primary" href="{{ route('Admin::Index::logout') }}"
           target="_parent">@lang('common.logout')&nbsp;[{{ session('backend_info.admin_name') }}]</a>
    </div>
    <div class="right_menu">
        <a class="btn btn-sm btn-primary" href="{{ route('Admin::Index::editMyPass') }}"
           target="main">@lang('common.edit')
            &nbsp;[{{ session('backend_info.admin_name') }}]&nbsp;{{ trans('common.pass') }}</a>
    </div>
    <div class="right_menu">
        <a class="btn btn-sm btn-primary" href="{{ route('Admin::Index::main') }}"
           target="main">@lang('common.backend')@lang('common.homepage')</a>
    </div>
    <div class="right_menu">
        <a class="btn btn-sm btn-primary" href="__ROOT__/"
           target="_blank">@lang('common.frontend')@lang('common.homepage')</a>
    </div>
</header>
</body>
</html>