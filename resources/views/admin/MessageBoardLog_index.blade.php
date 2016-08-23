
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('Public:where_info')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.audit') }}{{ trans('common.admin') }}</th>
                        <th>{{ trans('common.send') }}{{ trans('common.user') }}</th>
                        <th>{{ trans('common.send') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.send') }} IP</th>
                        <th>{{ trans('common.handle') }}</th>
                    </tr>
                    @foreach ($message_board_log_list as $message_board_log)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$message_board_log.id}"/>
                                &nbsp;{$message_board_log.id}
                            </td>
                            <td>
                                {$message_board_log.admin_name}
                            </td>
                            <td>
                                {$message_board_log.member_name}
                            </td>
                            <td>
                                {$message_board_log.add_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$message_board_log.aip}
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$message_board_log['id'])) }}">
                                        {{ trans('common.audit') }}
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}?','{{ route('del',array('id'=>$message_board_log['id'])) }}')" >
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
                                @if ($batch_handle['edit'])
                                    config.type_data.push({'name':$Think.lang.audit,'post_link':'{{ route('edit') }}','post_data':{'is_audit':'1'} });
                                    config.type_data.push({'name':$Think.lang.cancel+$Think.lang.audit,'post_link':'{{ route('edit') }}','post_data':{'is_audit':'0'} });
                                @endif
                                @if ($batch_handle['del'])
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{{ route('del') }}' });
                                @endif
                                new M_batch_handle(config);
                            });
                        </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="message_board_log_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>