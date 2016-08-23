
    <script type="text/javascript" src="{{ asset('js/M_alert_log.js') }}"></script>
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('Public:where_info')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.upload') }}{{ trans('common.person') }}</th>
                        <th>{{ trans('common.file') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.add') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.size') }}</th>
                        <th>{{ trans('common.suffix') }}</th>
                        <th>{{ trans('common.bind') }}{{ trans('common.info') }}</th>
                        <td class="nowrap">
                            @if ($batch_handle['edit'])
                                <a class="btn btn-xs btn-success"  href="{{ route('edit') }}">{{ trans('common.clear') }}{{ trans('common.none') }}{{ trans('common.bind') }}</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($manage_upload_list as $manage_upload)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $manage_upload['id'] }}"/>
                                &nbsp;{{ $manage_upload['id'] }}
                            </td>
                            <td>
                                {{ $manage_upload['user_name'] }}
                                [
                                    @if (1 eq $manage_upload['user_type']){{ trans('common.backend') }}
                                    @elseif (2 eq $manage_upload['user_type']){{ trans('common.frontend') }}
                                    @endif
                                ]
                            </td>
                            <td>
                                {{ $manage_upload['name'] }}
                            </td>
                            <td>
                                {{ $manage_upload['add_time']|M_date=C('SYS_DATE_DETAIL') }}
                            </td>
                            <td>
                                {{ $manage_upload['size'] }}
                            </td>
                            <td>
                                {{ $manage_upload['suffix'] }}
                            </td>
                            <td class="nowrap">
                                @if ($manage_upload['bind_info'])
<a id="M_alert_log_{{ $manage_upload['id'] }}" class="btn btn-xs btn-primary" href="javascript:void(0);" >{{ trans('common.look') }}</a>
                                    <script>
                                        $(function(){
                                            var config = {
                                                'bind_obj':$('#M_alert_log_{{ $manage_upload['id'] }}'),
                                                'title':'{{ trans('common.file') }}{{ trans('common.bind') }}{{ trans('common.info') }}',
                                                'message':{{ $manage_upload['bind_info'] }}
                                            }
                                            new M_alert_log(config);
                                        });
                                    </script>
                                @else
                                    {{ trans('common.empty') }}
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="javascript:void(0);" id="copy_obj{{ $manage_upload['id'] }}" data-clipboard-text="{{ $manage_upload['path'] }}" >
                                    <script type="text/javascript" charset="utf-8">M_ZeroClipboard('copy_obj{{ $manage_upload['id'] }}');</script>
                                    {{ trans('common.copy') }}{{ trans('common.path') }}
                                </a>
                                @if ($batch_handle['del'])
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{{ $manage_upload['name'] }}?','{{ route('del',array('id'=>$manage_upload['id'])) }}')" >
                                        {{ trans('common.del') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="row">
                    <div id="batch_handle"  class="col-sm-4 pagination">
                        @if ($batch_handle['del'])
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
                        <M:Page name="manage_upload_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
