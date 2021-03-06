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
                        <th>@lang('common.send')@lang('common.info')</th>
                        <th>@lang('common.receive')@lang('common.info')</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('Admin::Message::add') }}">@lang('common.send')@lang('message.message')</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($message_list as $message)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $message['id'] }}"/>
                                &nbsp;{{ $message['id'] }}
                            </td>
                            <td>
                                {{ $message['send_name'] }}
                                &nbsp;&nbsp;[{{ $message['created_at'] }}]
                            </td>
                            <td>
                                {{ $message['receive_name'] }}&nbsp;&nbsp;[
                                @if ($message['updated_at'] != $message['created_at'])
                                    {{ $message['updated_at'] }}
                                @else
                                    @lang('common.none')@lang('common.receive')
                                @endif]
                            </td>
                            <td class="nowrap">
                                <a id="M_alert_log_{{ $message['id'] }}" class="btn btn-xs btn-primary"
                                   href="javascript:void(0);">@lang('common.look')</a>
                                <script>
                                    $(function () {
                                        var config = {
                                            'bind_obj': $('#M_alert_log_{{ $message['id'] }}'),
                                            'title': '@lang('message.message')@lang('common.content')',
                                            'message': "{{ $message['content'] }}"
                                            @if (0 == $message['receive_id'] AND $message['updated_at'] == $message['created_at'])
                                            ,
                                            'cb_fn': M_alert_log_Message($('#M_alert_log_{{ $message['id'] }}'),{{ $message['id'] }}, '{{ route('Admin::Message::ajax_api') }}')
                                            @endif
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                @if ($batch_handle['add'])
                                    @if (0 == $message['receive_id'])
                                        &nbsp;|&nbsp;
                                        <a class="btn btn-xs btn-primary"
                                           href="{{ route('Admin::Message::add',array('receive_id'=>$message['send_id'])) }}">
                                            @lang('common.reply')@lang('message.message')
                                        </a>
                                    @endif
                                @endif
                                @if ($batch_handle['del'])
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del')?','{{ route('Admin::Message::del',array('id'=>$message['id'])) }}')">
                                        @lang('common.del')
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="row">
                    <div id="batch_handle" class="col-sm-4 pagination">
                        @if ($batch_handle['add'] OR $batch_handle['del'])
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
                                        'name': lang.common.del,
                                        'post_link': '{{ route('Admin::Message::del') }}'
                                    });
                                    @endif
                                        new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        {{ $message_list->links('admin.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection