@extends('install.layout')
@section('body')
    @push('scripts')
    <script type="text/javascript">
        $(function () {
            if (parent && parent.move_progress) {
                parent.move_progress({{ config('install.setp_progress.1') }});
            }
        });
    </script>
    @endpush
    {{--安装第一步界面 开始--}}
    <section class="container">
        <div class="row">
            <div class="col-sm-12">
                <form id="mysql_config" class="form-horizontal" action="">
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label">@lang('common.host')@lang('common.colon')</label>
                        <div class="col-sm-8"><input type="text" class="form-control" placeholder="@lang('common.host')"
                                                     value="{{ $default_config['DB_HOST'] }}" name="db_host"></div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label">@lang('common.database')@lang('common.colon')</label>
                        <div class="col-sm-4"><input type="text" class="form-control"
                                                     placeholder="@lang('common.database')"
                                                     value="{{ $default_config['DB_DATABASE'] }}" name="db_database"></div>
                        <div class="btn-group col-sm-4">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                @lang('common.selection')@lang('common.exists')@lang('common.database')<span
                                        class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                @foreach ($database_list as $data)
                                    <li class="text-left"><a href="#"
                                                             onclick="$('input[name=name]').val($(this).html())">{{ $data }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label">@lang('common.user')@lang('common.colon')</label>
                        <div class="col-sm-8"><input type="text" class="form-control" placeholder="@lang('common.user')"
                                                     value="{{ $default_config['DB_USERNAME'] }}" name="db_username"></div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label">@lang('common.pass')@lang('common.colon')</label>
                        <div class="col-sm-8"><input type="password" class="form-control"
                                                     placeholder="@lang('common.pass')"
                                                     value="{{ $default_config['DB_PASSWORD'] }}" name="db_password"></div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label">@lang('common.port')@lang('common.colon')</label>
                        <div class="col-sm-8"><input type="text" class="form-control" placeholder="@lang('common.port')"
                                                     value="{{ $default_config['DB_PORT'] }}" name="db_port"></div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col-sm-4 control-label">@lang('common.prefix')@lang('common.colon')</label>
                        <div class="col-sm-8"><input type="text" class="form-control"
                                                     placeholder="@lang('common.prefix')"
                                                     value="{{ $default_config['DB_PREFIX'] }}" name="db_prefix"></div>
                    </div>
                </form>
            </div>
            <div class="col-sm-12 text-center">
                <a class="btn btn-lg btn-primary mt20 mr80"
                   href="{{ route('Install::') }}">@lang('common.previous')@lang('common.setp')</a>
                <a id="mysql_config_btn" class="btn btn-lg btn-primary mt20"
                   href="javascript:void(0);">@lang('install.setp1')</a>
                <script type="text/javascript">
                    var config = {
                        'out_obj': '#mysql_config_btn',
                        'edit_obj': '#mysql_config',
                        'next_link': '{{ route('Install::setp2') }}',
                        'ajax_url': '{{ route('Install::ajax_api') }}'
                    }
                    new M_check_mysql(config);
                </script>
            </div>
            <div class="col-sm-12 text-center">
                <div id="show_box" class="mt20"></div>
            </div>
            <script type="text/javascript">
                @foreach ($note as $data)
                show_install_message("#show_box", "{{ $data }}@lang('common.extend')@lang('common.none')@lang('common.loading')", "warning")
                @endforeach
            </script>
        </div>
    </section>
    {{--安装第一步界面 结束--}}
@endsection