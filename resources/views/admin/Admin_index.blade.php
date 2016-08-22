
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.admin') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.management') }}{{ trans('common.group') }}</th>
                        <th>{{ trans('common.add') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.last') }}{{ trans('common.login') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.login') }} IP</th>
                        <th>{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.enable') }}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{:U('add')}">{{ trans('common.add') }}{{ trans('common.admin') }}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="admin_list" item="admin">
                        <tr>
                            <td>
                                <if condition="$admin['id'] neq 1">
                                    <input name="id[]" type="checkbox" value="{$admin.id}"/>
                                <else />
                                &nbsp;&nbsp;&nbsp;
                                </if>
                                &nbsp;{$admin.id}
                            </td>
                            <td>
                                {$admin.admin_name}
                            </td>
                            <td>
                                {$admin.group_name}
                            </td>
                            <td>
                                <if condition="0 lt $admin['add_time']">
                                    {$admin.add_time|M_date=C('SYS_DATE_DETAIL')}
                                <else />
                                    {{ trans('common.system') }}{{ trans('common.add') }}
                                </if>
                            </td>
                            <td>
                                {$admin.last_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$admin.aip}
                            </td>
                            <td>
                                <if condition="$admin['is_enable']">{{ trans('common.enable') }}<else />{{ trans('common.disable') }}</if>
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['edit']">
                                    <a class="btn btn-xs btn-primary" href="{:U('edit',array('id'=>$admin['id']))}">
                                        {{ trans('common.edit') }}
                                    </a>
                                </if>
                                <if condition="$batch_handle['edit'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}{$admin.admin_name}?','{:U('del',array('id'=>$admin['id']))}')" >
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
                                    config.type_data.push({'name':$Think.lang.enable,'post_link':'{:U('edit')}','post_data':{'is_enable':'1'} });
                                    config.type_data.push({'name':$Think.lang.disable,'post_link':'{:U('edit')}','post_data':{'is_enable':'0'} });
                                </if>
                                <if condition="$batch_handle['del']">
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{:U('del')}' });
                                </if>
                                new M_batch_handle(config);
                            });
                        </script>
                        </if>
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="admin_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
