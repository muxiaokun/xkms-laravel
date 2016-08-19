<extend name="Member:base" />
<block name="content">
    <import file="js/M_alert_log" />
    <table class="table table-condensed table-hover">
        <tr>
            <th>{$Think.lang.id}</th>
            <th>{$Think.lang.send}{$Think.lang.info}</th>
            <th>{$Think.lang.receive}{$Think.lang.info}</th>
            <th><a class="btn btn-xs btn-success" href="{:M_U('add')}">{$Think.lang.send}{$Think.lang.message}</a></th>
        </tr>
        <foreach name="message_list" item="message">
            <tr>
                <td>
                    <input name="id[]" type="checkbox" value="{$message.id}"/>
                    &nbsp;{$message.id}
                </td>
                <td>
                    {$message.send_name}&nbsp;&nbsp;[{$message.send_time|M_date=C('SYS_DATE_DETAIL')}]
                </td>
                <td>
                    {$message.receive_name}&nbsp;&nbsp;[
                    <if condition="0 lt $message['receive_time']">
                        {$message.receive_time|M_date=C('SYS_DATE_DETAIL')}
                    <else/>
                        {$Think.lang.none}{$Think.lang.receive}
                    </if>]
                </td>
                <td>
<a id="M_alert_log_{$message.id}" class="btn btn-xs btn-primary" href="javascript:void(0);" >{$Think.lang.look}</a>
                    <script>
                        $(function(){
                            var config = {
                                'bind_obj':$('#M_alert_log_{$message.id}'),
                                'title':'{$Think.lang.message}{$Think.lang.content}',
                                'message':"{$message.content}"
                                <if condition="session('frontend_info.id') eq $message['receive_id'] AND 0 eq $message['receive_time']">
                                ,'cb_fn':M_alert_log_Message($('#M_alert_log_{$message.id}'),{$message.id},'{:M_U("ajax_api")}')
                                </if>
                            }
                            new M_alert_log(config);
                        });
                    </script>
                    &nbsp;|&nbsp;
                    <if condition="0 lt $message['send_id']">
                    <a class="btn btn-xs btn-primary" href="{:M_U('add',array('receive_id'=>$message['send_id']))}">
                        {$Think.lang.reply}{$Think.lang.message}
                    </a>
                    &nbsp;|&nbsp;
                    </if>
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}?','{:M_U('del',array('id'=>$message['id']))}')" >
                        {$Think.lang.del}
                    </a>
                </td>
            </tr>
        </foreach>
    </table>
    <div id="batch_handle"  class="col-sm-4 pagination">
        <if condition="$batch_handle['edit'] OR $batch_handle['del']">
        <import file="js/M_batch_handle" />
        <script type="text/javascript">
            $(function(){
                var config = {
                    'out_obj':$('#batch_handle'),
                    'post_obj':'input[name="id"]',
                    'type_data':[]
                };
                <if condition="$batch_handle['del']">
                    config.type_data.push({'name':$Think.lang.del,'post_link':'{:M_U('del')}' });
                </if>
                new M_batch_handle(config);
            });
        </script>
        </if>
    </div>
    <M:Page name="message_list">
        <table class="table"><tr><td class="text-right">
            <config></config>
        </td></tr></table>
    </M:Page>
</block>