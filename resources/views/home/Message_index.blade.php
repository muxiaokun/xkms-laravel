@extends('home.Member_layout')
@section('content')
    <script type="text/javascript" src="{{ asset('js/M_alert_log.js') }}"></script>
    <table class="table table-condensed table-hover">
        <tr>
            <th>@lang('common.id')</th>
            <th>@lang('common.send')@lang('common.info')</th>
            <th>@lang('common.receive')@lang('common.info')</th>
            <th><a class="btn btn-xs btn-success"
                   href="{{ route('Home::Message::add') }}">@lang('common.send')@lang('common.message')</a>
            </th>
        </tr>
        @foreach ($message_list as $message)
            <tr>
                <td>
                    <input name="id[]" type="checkbox" value="{{ $message['id'] }}"/>
                    &nbsp;{{ $message['id'] }}
                </td>
                <td>
                    {{ $message['send_name'] }}
                    &nbsp;&nbsp;[{{ mDate($message['send_time']) }}]
                </td>
                <td>
                    {{ $message['receive_name'] }}&nbsp;&nbsp;[
                    @if (0 < $message['receive_time'])
                        {{ mDate($message['receive_time']) }}
                    @else
                        @lang('common.none')@lang('common.receive')
                    @endif]
                </td>
                <td>
                    <a id="M_alert_log_{{ $message['id'] }}" class="btn btn-xs btn-primary"
                       href="javascript:void(0);">@lang('common.look')</a>
                    <script>
                        $(function () {
                            var config = {
                                'bind_obj': $('#M_alert_log_{{ $message['id'] }}'),
                                'title': '@lang('common.message')@lang('common.content')',
                                'message': "{{ $message['content'] }}"
                                @if (session('frontend_info.id') == $message['receive_id'] AND 0 == $message['receive_time'])
                                ,
                                'cb_fn': M_alert_log_Message($('#M_alert_log_{{ $message['id'] }}'),{{ $message['id'] }}, '{{ route('Home::Message::ajax_api') }}')
                                @endif
                            }
                            new M_alert_log(config);
                        });
                    </script>
                    &nbsp;|&nbsp;
                    @if (0 < $message['send_id'])
                        <a class="btn btn-xs btn-primary"
                           href="{{ route('Home::Message::add',['receive_id'=>$message['send_id']]) }}">
                            @lang('common.reply')@lang('common.message')
                        </a>
                        &nbsp;|&nbsp;
                    @endif
                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del')?','{{ route('Home::Message::del',['id'=>$message['id']]) }}')">
                        @lang('common.del')
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
    <div id="batch_handle" class="col-sm-4 pagination">
        @if ($batch_handle['edit'] OR $batch_handle['del'])
            <script type="text/javascript" src="{{ asset('js/M_batch_handle.js') }}"></script>
            <script type="text/javascript">
                $(function () {
                    var config = {
                        'out_obj': $('#batch_handle'),
                        'post_obj': 'input[name="id"]',
                        'type_data': []
                    };
                    @if ($batch_handle['del'])
                        config.type_data.push({
                        'name': lang.common.del,
                        'post_link': '{{ route('Home::Message::del') }}'
                    });
                    @endif
                        new M_batch_handle(config);
                });
            </script>
        @endif
    </div>
    <table class="table">
        <tr>
            <td class="text-right">
                {{ $message_list->links('home.pagination') }}
            </td>
        </tr>
    </table>
@endsection