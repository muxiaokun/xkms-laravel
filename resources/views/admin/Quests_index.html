
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.title}</th>
                        <th>{$Think.lang.portion}</th>
                        <th>{$Think.lang.start}{$Think.lang.time}</th>
                        <th>{$Think.lang.end}{$Think.lang.time}</th>
                        <th>{$Think.lang.access}{$Think.lang.pass}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{:U('add')}">{$Think.lang.add}{$Think.lang.quests}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="quests_list" item="quests">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$quests.id}"/>
                                &nbsp;{$quests.id}
                            </td>
                            <td>
                                {$quests.title}
                            </td>
                            <td>
                                {$quests.current_portion}/{$quests.max_portion}
                            </td>
                            <td>
                                {$quests.start_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$quests.end_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$quests.access_info}
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['answer_index']">
                                    <a class="btn btn-xs btn-primary" href="{:U('QuestsAnswer/index',array('quests_id'=>$quests['id']))}">
                                        {$Think.lang.answer}{$Think.lang.list}
                                    </a>
                                </if>
                                <if condition="$batch_handle['answer_index'] AND $batch_handle['answer_edit']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['answer_edit']">
                                    <a class="btn btn-xs btn-primary" href="{:U('QuestsAnswer/edit',array('quests_id'=>$quests['id']))}">
                                        {$Think.lang.statistics}{$Think.lang.quests}
                                    </a>
                                </if>
                                <if condition="$batch_handle['answer_edit'] AND $batch_handle['edit']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['edit']">
                                    <a class="btn btn-xs btn-primary" href="{:U('edit',array('id'=>$quests['id']))}">
                                        {$Think.lang.edit}
                                    </a>
                                </if>
                                <if condition="$batch_handle['edit'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.clear}{$quests.title}{$Think.lang.answer}?',
'{:U('del',array('id'=>$quests['id'],'clear'=>1))}')" >
                                        {$Think.lang.clear}{$Think.lang.answer}
                                    </a>
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}{$quests.title}?','{:U('del',array('id'=>$quests['id']))}')" >
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
                        <M:Page name="quests_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
