
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">
                {$title}
                <a class="fr fs10" href="{:U('Quests/index',array('id'=>I('id')))}">{$Think.lang.goback}</a>
            </div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.title}</th>
                        <th>{$Think.lang.add}{$Think.lang.time}</th>
                        <th>{$Think.lang.handle}</th>
                    </tr>
                    <foreach name="quests_answer_list" item="quests_answer">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$quests_answer.id}"/>
                                &nbsp;{$quests_answer.id}
                            </td>
                            <td>
                                {$quests_answer.member_name}
                            </td>
                            <td>
                                {$quests_answer.add_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['add']">
                                    <a class="btn btn-xs btn-primary" href="{:U('QuestsAnswer/add',array('id'=>$quests_answer['id']))}">
                                        {$Think.lang.look}
                                    </a>
                                </if>
                                <if condition="$batch_handle['add'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}?','{:U('del',array('id'=>$quests_answer['id']))}')" >
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
                        <M:Page name="quests_answer_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
