<script type="text/javascript" src="{{ asset('js/M_alert_log.js') }}"></script>
<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            @include('Public:where_info')
            <table class="table table-condensed table-hover">
                <tr>
                    <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')</th>
                    <th>@lang('common.admin')@lang('common.name')</th>
                    <th>@lang('common.add')@lang('common.time')</th>
                    <th>@lang('common.module')@lang('common.name')</th>
                    <th>@lang('common.controller')@lang('common.name')</th>
                    <th>@lang('common.action')@lang('common.name')</th>
                    <th>@lang('common.model')@lang('common.name')</th>
                    <th>@lang('common.request')</th>
                    <th class="nowrap">
                        @if (session('backend_info.id') != 1)
                            @lang('common.handle')
                        @else
                            <a class="btn btn-xs btn-danger"
                               href="{{ route('del_all') }}">@lang('common.del')@lang('common.all')</a>
                        @endif
                    </th>
                </tr>
                @foreach ($admin_log_list as $admin_log)
                    <tr>
                        <td>
                            <input name="id[]" type="checkbox" value="{{ $admin_log['id'] }}"/>
                            &nbsp;{{ $admin_log['id'] }}
                        </td>
                        <td>
                            {{ $admin_log['admin_name'] }}
                        </td>
                        <td>
                            {{ $admin_log['add_time']|M_date=C('SYS_DATE_DETAIL') }}
                        </td>
                        <td>
                            {{ $admin_log['module_name'] }}
                        </td>
                        <td>
                            {{ $admin_log['controller_name'] }}
                        </td>
                        <td>
                            {{ $admin_log['action_name'] }}
                        </td>
                        <td>
                            {{ $admin_log['model_name'] }}
                        </td>
                        <td>
                            @if (2 < strlen($admin_log['request']))
                                <a id="M_alert_log_{{ $admin_log['id'] }}" class="btn btn-xs btn-primary"
                                   href="javascript:void(0);">@lang('common.look')</a>
                                <script>
                                    $(function () {
                                        var config = {
                                            'bind_obj': $('#M_alert_log_{{ $admin_log['id'] }}'),
                                            'title': '@lang('common.admin')@lang('common.handle')@lang('common.log')',
                                            'message':{{ $admin_log['request'] }}
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                            @endif
                        </td>
                        <td class="nowrap">
                            @if ($batch_handle['del'])
                                <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                   onClick="return M_confirm('@lang('common.confirm')@lang('common.del')?','{{ route("del",array("id"=>$admin_log['id'])) }}')">
                                    @lang('common.del')
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            <div class="row">
                <div id="batch_handle" class="col-sm-4 pagination">
                    @if ($batch_handle['del'])
                        <script type="text/javascript" src="{{ asset('js/M_batch_handle.js') }}"></script>
                        <script type="text/javascript">
                            $(function () {
                                var config = {
                                    'out_obj': $('#batch_handle'),
                                    'post_obj': 'input[name="id"]',
                                    'type_data': Array()
                                };
                                @if ($batch_handle['del'])
                                    config.type_data.push({'name': $Think.lang.del, 'post_link': '{{ route('del') }}'});
                                @endif
                                        new M_batch_handle(config);
                            });
                        </script>
                    @endif
                </div>
                <div class="col-sm-8 text-right">
                    <M:Page name="admin_log_list">
                        <config></config>
                    </M:Page>
                </div>
            </div>
        </div>
    </div>
</section>
