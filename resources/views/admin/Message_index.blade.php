
    <script type="text/javascript" src="{{ asset('js/M_alert_log.js') }}"></script>
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('Public:where_info')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.send') }}{{ trans('common.info') }}</th>
                        <th>{{ trans('common.receive') }}{{ trans('common.info') }}</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success" href="{{ route('add') }}">{{ trans('common.send') }}{{ trans('common.message') }}</a>
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
                                {{ $message['send_name'] }}&nbsp;&nbsp;[{{ $message['send_time']|M_date=C('SYS_DATE_DETAIL') }}]
                            </td>
                            <td>
                                {{ $message['receive_name'] }}&nbsp;&nbsp;[
                                @if (0 lt $message['receive_time'])
                                    {{ $message['receive_time']|M_date=C('SYS_DATE_DETAIL') }}
                                @else
                                    {{ trans('common.none') }}{{ trans('common.receive') }}
                                @endif]
                            </td>
                            <td class="nowrap">
<a id="M_alert_log_{{ $message['id'] }}" class="btn btn-xs btn-primary" href="javascript:void(0);" >{{ trans('common.look') }}</a>
                                <script>
                                    $(function(){
                                        var config = {
                                            'bind_obj':$('#M_alert_log_{{ $message['id'] }}'),
                                            'title':'{{ trans('common.message') }}{{ trans('common.content') }}',
                                            'message':"{{ $message['content'] }}"
                                            @if (0 eq $message['receive_id'] AND 0 eq $message['receive_time'])
                                            ,'cb_fn':M_alert_log_Message($('#M_alert_log_{{ $message['id'] }}'),{{ $message['id'] }},'{:M_U("ajax_api")}')
                                            @endif
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                @if ($batch_handle['add'])
                                    &nbsp;|&nbsp;
                                    @if (0 lt $message['send_id'])
                                    <a class="btn btn-xs btn-primary" href="{{ route('add',array('receive_id'=>$message['send_id'])) }}">
                                        {{ trans('common.reply') }}{{ trans('common.message') }}
                                    </a>
                                    @endif
                                @endif
                                @if ($batch_handle['del'])
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}?','{{ route('del',array('id'=>$message['id'])) }}')" >
                                        {{ trans('common.del') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="row">
                    <div id="batch_handle"  class="col-sm-4 pagination">
                        @if ($batch_handle['edit'] OR $batch_handle['del'])
                        <script type="text/javascript" src="{{ asset('js/M_batch_handle.js') }}"></script>
                        <script type="text/javascript">
                            $(function(){
                                var config = {
                                    'out_obj':$('#batch_handle'),
                                    'post_obj':'input[name="id"]',
                                    'type_data':Array()
                                };
                                @if ($batch_handle['del'])
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{{ route('del') }}' });
                                @endif
                                new M_batch_handle(config);
                            });
                        </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="message_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>