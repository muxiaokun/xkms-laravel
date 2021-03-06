@extends('admin.layout')
@section('body')
    <section class="container">
        <div class="row">
            <div class="col-sm-12 page-header">
                <h1>@lang('common.welcome')@lang('common.use')@lang('common.app_name')</h1>
                <a href="{{ route('Admin::Index::cleanCache') }}"
                   class="hidden">@lang('common.clean')@lang('common.cache')</a>
            </div>
            <div class="col-sm-12 mb20 text-center quick_ico">
                @if ($site_info['ico']['ico1'])
                    <a href="{{ route('Admin::Article::add') }}"><span class="mr20 ico1"
                                                                       title="@lang('common.add')@lang('common.article')"></span></a>
                @endif
                @if ($site_info['ico']['ico2'])
                    <a href="{{ route('Admin::ArticleCategory::add') }}"><span class="mr20 ico2"
                                                                               title="@lang('common.add')@lang('common.article')@lang('common.category')"></span></a>
                @endif
                @if ($site_info['ico']['ico6'])
                    <a href="{{ route('Admin::Member::add') }}"><span class="mr20 ico6"
                                                                      title="@lang('common.add')@lang('common.member')"></span></a>
                @endif
                @if ($site_info['ico']['ico7'])
                    <a href="{{ route('Admin::MemberGroup::add') }}"><span class="mr20 ico7"
                                                                           title="@lang('common.add')@lang('common.member')@lang('common.group')"></span></a>
                @endif
                @if ($site_info['ico']['ico8'])
                    <a href="{{ route('Admin::ManageUpload::index') }}"><span class="mr20 ico8"
                                                                              title="@lang('common.management')@lang('common.upload')@lang('common.file')"></span></a>
                @endif
                @if ($site_info['ico']['ico9'])
                    <a href="{{ route('Admin::Index::cleanLog') }}"><span class="mr20 ico9"
                                                                          title="@lang('common.clean')@lang('common.log')"></span></a>
                @endif
                @if ($site_info['ico']['ico10'])
                    <a href="{{ route('Admin::Index::cleanCache') }}"><span class="mr20 ico10"
                                                                            title="@lang('common.clean')@lang('common.cache')"></span></a>
                @endif
                @if ($site_info['ico']['ico12'])
                    <a href="{{ route('Admin::Index::databaseSet') }}"><span class="mr20 ico12"
                                                                             title="@lang('common.database')@lang('common.config')"></span></a>
                @endif
                <script type="text/javascript">M_jqueryui_tooltip('.quick_ico span')</script>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('common.app_name')@lang('common.news')</div>
                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <tr id="news_row" style="display:none">
                                <td><a href="javascript:void(0);" target="_blank">
                                    <span class="col-sm-9" mtype="title"
                                          style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"></span>
                                        <span class="col-sm-3" mtype="date"></span>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    @if (!config('app.debug'))
                        <script type="text/javascript">
                            //加载站异步的新闻
                            $(function () {
                                $.getJSON('http://www.example.com/news_api.php?callback=?', function (data) {
                                    if (!data)return;
                                    var t_obj = $('#news_row');
                                    var tp_obj = t_obj.parent();
                                    var max = 4;
                                    $.each(data, function (k, v) {
                                        if (k >= max)return;
                                        var new_obj = t_obj.clone();
                                        new_obj.find('[mtype=title]').html(v.title);
                                        new_obj.find('[mtype=date]').html(v.date);
                                        new_obj.find('a').attr('href', v.link);
                                        new_obj.show();
                                        tp_obj.append(new_obj);
                                    });
                                });
                            });
                        </script>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('common.current')@lang('common.account')@lang('common.info')</div>
                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <tr>
                                <td>@lang('common.account')@lang('common.name')</td>
                                <td>{{ session('backend_info.admin_name')  }}</td>
                            </tr>
                            <tr>
                                <td>@lang('common.register')@lang('common.time')</td>
                                <td>{{ session('backend_info.created_at') }}</td>
                            </tr>
                            <tr>
                                <td>@lang('common.login')@lang('common.time')</td>
                                <td>{{ session('backend_info.last_time') }}</td>
                            </tr>
                            <tr>
                                <td>@lang('common.login')IP</td>
                                <td>{{ mIptoadd(session('backend_info.login_ip')) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('common.current')@lang('common.system')@lang('common.info')</div>
                    <div class="panel-body">
                        <table class="table table-condensed table-hover">
                            <tr>
                                <td class="col-sm-2">@lang('common.system')@lang('common.version')</td>
                                <td class="col-sm-4">{{ $site_info['sys_version'] }}</td>
                                <td class="col-sm-2">@lang('common.system')@lang('common.timezone')</td>
                                <td class="col-sm-4">{{ $site_info['sys_timezone'] }}</td>
                            </tr>
                            <tr>
                                <td>@lang('common.server')IP</td>
                                <td>{{ $site_info['server_ip'] }}</td>
                                <td>@lang('common.max')@lang('common.upload')@lang('common.limit')</td>
                                <td>{{ $site_info['max_upload_size'] }}</td>
                            </tr>
                            <tr>
                                <td>PHP @lang('common.version')</td>
                                <td>{{ $site_info['php_version'] }}</td>
                                <td>MySql @lang('common.version')</td>
                                <td>{{ $site_info['mysql_version'] }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection