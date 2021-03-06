@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('admin.Public_whereInfo')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')
                        </th>
                        <th>@lang('common.audit')@lang('common.admin')</th>
                        <th>@lang('common.send')@lang('common.user')</th>
                        <th>@lang('common.send')@lang('common.time')</th>
                        <th>@lang('common.send') IP</th>
                        <th>@lang('common.handle')</th>
                    </tr>
                    @foreach ($message_board_log_list as $message_board_log)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $message_board_log['id'] }}"/>
                                &nbsp;{{ $message_board_log['id'] }}
                            </td>
                            <td>
                                {{ $message_board_log['admin_name'] }}
                            </td>
                            <td>
                                {{ $message_board_log['member_name'] }}
                            </td>
                            <td>
                                {{ $message_board_log['created_at'] }}
                            </td>
                            <td>
                                {{ $message_board_log['add_ip'] }}
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('Admin::MessageBoardLog::edit',array('id'=>$message_board_log['id'])) }}">
                                        @lang('common.audit')
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del')?','{{ route('Admin::MessageBoardLog::del',array('id'=>$message_board_log['id'])) }}')">
                                        @lang('common.del')
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="row">
                    <div id="batch_handle" class="col-sm-4 pagination">
                        @if ($batch_handle['edit'] OR $batch_handle['del'])
                            <script type="text/javascript" src="{{ asset('js/M_batch_handle.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        'out_obj': $('#batch_handle'),
                                        'post_obj': 'input[name="id"]',
                                        'type_data': Array()
                                    };
                                    @if ($batch_handle['edit'])
                                        config.type_data.push({
                                        'name': lang.common.audit,
                                        'post_link': '{{ route('Admin::MessageBoardLog::edit') }}',
                                        'post_data': {'audit_id': '1'}
                                    });
                                    config.type_data.push({
                                        'name': lang.common.cancel + lang.common.audit,
                                        'post_link': '{{ route('Admin::MessageBoardLog::edit') }}',
                                        'post_data': {'audit_id': '0'}
                                    });
                                    @endif
                                    @if ($batch_handle['del'])
                                        config.type_data.push({
                                        'name': lang.common.del,
                                        'post_link': '{{ route('Admin::MessageBoardLog::del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        {{ $message_board_log_list->links('admin.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection