
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.member') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.member') }}{{ trans('common.group') }}</th>
                        <th>{{ trans('common.register') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.last') }}{{ trans('common.login') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.login') }} IP</th>
                        <th>{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.enable') }}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{{ route('add') }}">{{ trans('common.add') }}{{ trans('common.member') }}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="member_list" item="member">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$member.id}"/>
                                &nbsp;{$member.id}
                            </td>
                            <td>
                                {$member.member_name}
                            </td>
                            <td>
                                {$member.group_name}
                            </td>
                            <td>
                                {$member.register_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$member.last_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$member.aip}
                            </td>
                            <td>
                                <if condition="$member['is_enable']">{{ trans('common.enable') }}<else />{{ trans('common.disable') }}</if>
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['edit']">
                                    <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$member['id'])) }}">
                                        {{ trans('common.edit') }}
                                    </a>
                                </if>
                                <if condition="$batch_handle['edit'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{$member.member_name}?','{{ route('del',array('id'=>$member['id'])) }}')" >
                                        {{ trans('common.del') }}
                                    </a>
                                </if>
                            </td>
                        </tr>
                    </foreach>
                </table>
                <div class="row">
                    <div id="batch_handle"  class="col-sm-4 pagination">
                        <if condition="$batch_handle['edit'] OR $batch_handle['del']">
                        <import file="js/M_batch_handle" />
                        <script type="text/javascript">
                            $(function(){
                                var config = {
                                    'out_obj':$('#batch_handle'),
                                    'post_obj':'input[name="id"]',
                                    'type_data':Array()
                                };
                                <if condition="$batch_handle['edit']">
                                    config.type_data.push({'name':$Think.lang.enable,'post_link':'{{ route('edit') }}','post_data':{'is_enable':'1'} });
                                    config.type_data.push({'name':$Think.lang.disable,'post_link':'{{ route('edit') }}','post_data':{'is_enable':'0'} });
                                </if>
                                <if condition="$batch_handle['del']">
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{{ route('del') }}' });
                                </if>
                                new M_batch_handle(config);
                            });
                        </script>
                        </if>
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="member_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>