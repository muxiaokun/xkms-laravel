    
    <import file="js/M_alert_log" />
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{{ trans('common.id') }}</th>
                        <th>{{ trans('common.admin') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.add') }}{{ trans('common.time') }}</th>
                        <th>{{ trans('common.module') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.controller') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.action') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.model') }}{{ trans('common.name') }}</th>
                        <th>{{ trans('common.request') }}</th>
                        <th class="nowrap">
                            <if condition="session('backend_info.id') neq 1">
                                {{ trans('common.handle') }}
                            <else />
                                <a class="btn btn-xs btn-danger" href="{:U('del_all')}">{{ trans('common.del') }}{{ trans('common.all') }}</a>
                            </if>
                        </th>
                    </tr>
                    <foreach name="admin_log_list" item="admin_log">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$admin_log.id}"/>
                                &nbsp;{$admin_log.id}
                            </td>
                            <td>
                                {$admin_log.admin_name}
                            </td>
                            <td>
                                {$admin_log.add_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$admin_log.module_name}
                            </td>
                            <td>
                                {$admin_log.controller_name}
                            </td>
                            <td>
                                {$admin_log.action_name}
                            </td>
                            <td>
                                {$admin_log.model_name}
                            </td>
                            <td>
                                <if condition="2 lt strlen($admin_log['request'])">
<a id="M_alert_log_{$admin_log.id}" class="btn btn-xs btn-primary" href="javascript:void(0);" >{{ trans('common.look') }}</a>
                                <script>
                                    $(function(){
                                        var config = {
                                            'bind_obj':$('#M_alert_log_{$admin_log.id}'),
                                            'title':'{{ trans('common.admin') }}{{ trans('common.handle') }}{{ trans('common.log') }}',
                                            'message':{$admin_log.request}
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                </if>
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{{ trans('common.confirm') }}{{ trans('common.del') }}?','{:U("del",array("id"=>$admin_log['id']))}')" >
                                        {{ trans('common.del') }}
                                    </a>
                                </if>
                            </td>
                        </tr>
                    </foreach>
                </table>
                <div class="row">
                    <div id="batch_handle"  class="col-sm-4 pagination">
                        <if condition="$batch_handle['del']">
                        <import file="js/M_batch_handle" />
                        <script type="text/javascript">
                            $(function(){
                                var config = {
                                    'out_obj':$('#batch_handle'),
                                    'post_obj':'input[name="id"]',
                                    'type_data':Array()
                                };
                                <if condition="$batch_handle['del']">
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{:U('del')}' });
                                </if>
                                new M_batch_handle(config);
                            });
                        </script>
                        </if>
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="admin_log_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
