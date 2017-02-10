@extends('admin.layout')
@section('body')
    <script type="text/javascript" src="{{ asset('js/M_alert_log.js') }}"></script>
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('admin.Public_whereInfo')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')
                        </th>
                        <th>@lang('common.admin')@lang('common.name')</th>
                        <th>@lang('common.add')@lang('common.time')</th>
                        <th>@lang('common.route')@lang('common.name')</th>
                        <th>@lang('common.request')</th>
                        <th class="nowrap">
                            @if (session('backend_info.id') != 1)
                                @lang('common.handle')
                            @else
                                <a class="btn btn-xs btn-danger"
                                   href="{{ route('Admin::AdminLog::del_all') }}">@lang('common.del')@lang('common.all')</a>
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
                                {{ mDate($admin_log['created_at']) }}
                            </td>
                            <td>
                                {{ $admin_log['route_name'] }}
                            </td>
                            <td>
                                @if ($admin_log['request'])
                                    <a id="M_alert_log_{{ $admin_log['id'] }}" class="btn btn-xs btn-primary"
                                       href="javascript:void(0);">@lang('common.look')</a>
                                    <script>
                                        $(function () {
                                            var config = {
                                                'bind_obj': $('#M_alert_log_{{ $admin_log['id'] }}'),
                                                'title': '@lang('common.admin')@lang('common.handle')@lang('common.log')',
                                                'message':{!! json_encode($admin_log['request']) !!}
                                            }
                                            new M_alert_log(config);
                                        });
                                    </script>
                                @endif
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['del'])
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del')?','{{ route("Admin::AdminLog::del",array("id"=>$admin_log['id'])) }}')">
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
                                        config.type_data.push({
                                        'name': lang.commondel,
                                        'post_link': '{{ route('Admin::AdminLog::del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        {{ $admin_log_list->links('admin.pagination') }}
                        <M:Page name="admin_log_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection