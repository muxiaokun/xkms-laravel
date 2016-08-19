
    <import file="js/M_alert_log" />
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.comment}{$Think.lang.member}</th>
                        <th>{$Think.lang.comment}{$Think.lang.time}</th>
                        <th>{$Think.lang.audit}{$Think.lang.admin}</th>
                        <th>{$Think.lang.controller}</th>
                        <th>{$Think.lang.id}</th>
                        <th>{$Think.lang.comment} IP</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{:U('add')}">{$Think.lang.config}{$Think.lang.comment}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="comment_list" item="comment">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$comment.id}"/>
                                &nbsp;{$comment.id}
                            </td>
                            <td>
                                {$comment.member_name}
                            </td>
                            <td>
                                {$comment.add_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$comment.audit_name}
                            </td>
                            <td>
                                {$comment.controller}
                            </td>
                            <td>
                                {$comment.item}
                            </td>
                            <td>
                                {$comment.aip}
                            </td>
                            <td class="nowrap">
<a id="M_alert_log_{$comment.id}" class="btn btn-xs btn-primary" href="javascript:void(0);">{$Think.lang.look}</a>
                                <script>
                                    $(function(){
                                        var config = {
                                            'bind_obj':$('#M_alert_log_{$comment.id}'),
                                            'title':'{$Think.lang.comment}{$Think.lang.content}',
                                            'message':"{$comment.content}"
                                        }
                                        new M_alert_log(config);
                                    });
                                </script>
                                <if condition="$batch_handle['edit']">
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-primary" href="{:U('edit',array('id'=>$comment['id']))}">
                                        {$Think.lang.audit}
                                    </a>
                                </if>
                                <if condition="$batch_handle['del']">
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}{$Think.lang.comment}?','{:U('del',array('id'=>$comment['id']))}')" >
                                        {$Think.lang.del}
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
                                    config.type_data.push({'name':$Think.lang.audit,'post_link':'{:U('edit')}'});
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
                        <M:Page name="comment_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>