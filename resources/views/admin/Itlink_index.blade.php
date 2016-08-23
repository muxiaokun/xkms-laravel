
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('Public:where_info')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.itlink') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.short') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.enable') }}</th>
                        <th>{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.statistics') }}</th>
                        <th>{{ trans('common.show') }}{{ trans('common.statistics') }}</th>
                        <th>{{ trans('common.click') }}{{ trans('common.statistics') }}</th>
                        <td class="nowrap">
                            @if ($batch_handle['edit'])
                                <a class="btn btn-xs btn-success"  href="{{ route('add') }}">{{ trans('common.add') }}{{ trans('common.itlink') }}</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($itlink_list as $itlink)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $itlink['id'] }}"/>
                                &nbsp;{{ $itlink['id'] }}
                            </td>
                            <td>
                                {{ $itlink['name'] }}
                            </td>
                            <td>
                                {{ $itlink['short_name'] }}
                            </td>
                            <td>
                                @if ($itlink['is_enable']){{ trans('common.yes') }}@else{{ trans('common.no') }}@endif
                            </td>
                            <td>
                                @if ($itlink['is_statistics']){{ trans('common.yes') }}@else{{ trans('common.no') }}@endif
                            </td>
                            <td>
                                {{ $itlink['show_num'] }}
                            </td>
                            <td>
                                {{ $itlink['hit_num'] }}
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary"  href="{{ route('edit',array('id'=>$itlink['id'])) }}">
                                        {{ trans('common.edit') }}
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{{ $itlink['name'] }}?','{{ route('del',array('id'=>$itlink['id'])) }}')" >
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
                                    config.type_data.push({'name':$Think.lang.enable,'post_link':'{{ route('edit') }}','post_data':{'is_enable':'1'} });
                                    config.type_data.push({'name':$Think.lang.disable,'post_link':'{{ route('edit') }}','post_data':{'is_enable':'0'} });
                                    config.type_data.push({'name':$Think.lang.statistics,'post_link':'{{ route('edit') }}','post_data':{'is_statistics':'1'} });
                                    config.type_data.push({'name':$Think.lang.cancel+$Think.lang.statistics,'post_link':'{{ route('edit') }}','post_data':{'is_statistics':'0'} });
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
                        <M:Page name="itlink_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>