    
    <import file="js/M_alert_log" />
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.admin}{$Think.lang.name}</th>
                        <th>{$Think.lang.add}{$Think.lang.time}</th>
                        <th>{$Think.lang.module}{$Think.lang.name}</th>
                        <th>{$Think.lang.controller}{$Think.lang.name}</th>
                        <th>{$Think.lang.action}{$Think.lang.name}</th>
                        <th>{$Think.lang.model}{$Think.lang.name}</th>
                        <th>{$Think.lang.request}</th>
                        <th class="nowrap">
                            <if condition="session('backend_info.id') neq 1">
                                {$Think.lang.handle}
                            <else />
                                <a class="btn btn-xs btn-danger" href="{:U('del_all')}">{$Think.lang.del}{$Think.lang.all}</a>
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
<a id="M_alert_log_{$admin_log.id}" class="btn btn-xs btn-primary" href="javascript:void(0);" >{$Think.lang.look}</a>
                                <script>
                                    $(function(){
                                        var config = {
                                            'bind_obj':$('#M_alert_log_{$admin_log.id}'),
                                            'title':'{$Think.lang.admin}{$Think.lang.handle}{$Think.lang.log}',
                                            'message':{$admin_log.request}
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                </if>
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}?','{:U("del",array("id"=>$admin_log['id']))}')" >
                                        {$Think.lang.del}
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
