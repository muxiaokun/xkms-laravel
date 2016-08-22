
    <import file="js/M_alert_log" />
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.upload}{$Think.lang.person}</th>
                        <th>{$Think.lang.file}{$Think.lang.name}</th>
                        <th>{$Think.lang.add}{$Think.lang.time}</th>
                        <th>{$Think.lang.size}</th>
                        <th>{$Think.lang.suffix}</th>
                        <th>{$Think.lang.bind}{$Think.lang.info}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['edit']">
                                <a class="btn btn-xs btn-success"  href="{:U('edit')}">{$Think.lang.clear}{$Think.lang.none}{$Think.lang.bind}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="manage_upload_list" item="manage_upload">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$manage_upload.id}"/>
                                &nbsp;{$manage_upload.id}
                            </td>
                            <td>
                                {$manage_upload.user_name}
                                [
                                    <if condition="1 eq $manage_upload['user_type']">{$Think.lang.backend}
                                    <elseif condition="2 eq $manage_upload['user_type']"/>{$Think.lang.frontend}
                                    </if>
                                ]
                            </td>
                            <td>
                                {$manage_upload.name}
                            </td>
                            <td>
                                {$manage_upload.add_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$manage_upload.size}
                            </td>
                            <td>
                                {$manage_upload.suffix}
                            </td>
                            <td class="nowrap">
                                <if condition="$manage_upload['bind_info']">
<a id="M_alert_log_{$manage_upload.id}" class="btn btn-xs btn-primary" href="javascript:void(0);" >{$Think.lang.look}</a>
                                    <script>
                                        $(function(){
                                            var config = {
                                                'bind_obj':$('#M_alert_log_{$manage_upload.id}'),
                                                'title':'{$Think.lang.file}{$Think.lang.bind}{$Think.lang.info}',
                                                'message':{$manage_upload.bind_info}
                                            }
                                            new M_alert_log(config);
                                        });
                                    </script>
                                <else/>
                                    {$Think.lang.empty}
                                </if>
                            </td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="javascript:void(0);" id="copy_obj{$manage_upload.id}" data-clipboard-text="{$manage_upload.path}" >
                                    <script type="text/javascript" charset="utf-8">M_ZeroClipboard('copy_obj{$manage_upload.id}');</script>
                                    {$Think.lang.copy}{$Think.lang.path}
                                </a>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}{$manage_upload.name}?','{:U('del',array('id'=>$manage_upload['id']))}')" >
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
                        <M:Page name="manage_upload_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
