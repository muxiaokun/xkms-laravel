
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('Public:where_info')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.name') }}</th>
                        <th>{{ trans('common.option') }}</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success" href="{{ route('add') }}">{{ trans('common.add') }}{{ trans('common.messageboard') }}</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($message_board_list as $message_board)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $message_board['id'] }}"/>
                                &nbsp;{{ $message_board['id'] }}
                            </td>
                            <td>
                                {{ $message_board['name'] }}
                            </td>
                            <td>
                                {{ $message_board['option'] }}
                            <td class="nowrap">
                                @if ($batch_handle['log_index'])
                                    <a class="btn btn-xs btn-primary" href="{{ route('MessageBoardLog/index',array('msg_id'=>$message_board['id'])) }}">
                                        {{ trans('common.look') }}
                                    </a>
                                @endif
                                @if ($batch_handle['log_index'] AND $batch_handle['edit'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$message_board['id'])) }}">
                                        {{ trans('common.edit') }}
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{{ $message_board['name'] }}?','{{ route('del',array('id'=>$message_board['id'])) }}')" >
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
                        <M:Page name="message_board_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>