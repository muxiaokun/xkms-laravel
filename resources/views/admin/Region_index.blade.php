
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('Public:where_info')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.region_name') }}</th>
                        <th>{{ trans('common.short_name') }}</th>
                        <th>{{ trans('common.all_spell') }}</th>
                        <th>{{ trans('common.short_spell') }}</th>
                        <th>{{ trans('common.areacode') }}</th>
                        <th>{{ trans('common.postcode') }}</th>
                        <th>{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.show') }}</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"  href="{{ route('add') }}">{{ trans('common.add') }}{{ trans('common.region') }}</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($region_list as $region)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $region['id'] }}"/>
                                &nbsp;{{ $region['id'] }}
                            </td>
                            <td>
                                @if ($region['parent_name'])[{{ $region['parent_name'] }}]&nbsp;&nbsp;@endif{{ $region['region_name'] }}
                            </td>
                            <td>
                                {{ $region['short_name'] }}
                            </td>
                            <td>
                                {{ $region['all_spell'] }}
                            </td>
                            <td>
                                {{ $region['short_spell'] }}
                            </td>
                            <td>
                                {{ $region['areacode'] }}
                            </td>
                            <td>
                                {{ $region['postcode'] }}
                            </td>
                            <td>
                                @if ($region['if_show']){{ trans('common.show') }}@else{{ trans('common.hidden') }}@endif
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary"  href="{{ route('edit',array('id'=>$region['id'])) }}">
                                        {{ trans('common.edit') }}
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{{ $region['name'] }}?','{{ route('del',array('id'=>$region['id'])) }}')" >
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
                                    config.type_data.push({'name':$Think.lang.show,'post_link':'{{ route('edit') }}','post_data':{'if_show':'1'} });
                                    config.type_data.push({'name':$Think.lang.hidden,'post_link':'{{ route('edit') }}','post_data':{'if_show':'0'} });
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
                        <M:Page name="region_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>