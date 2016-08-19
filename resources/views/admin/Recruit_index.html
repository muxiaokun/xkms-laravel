
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.recruit}{$Think.lang.name}</th>
                        <th>{$Think.lang.yes}{$Think.lang.no}{$Think.lang.enable}</th>
                        <th>{$Think.lang.re_recruit}{$Think.lang.number}</th>
                        <th>{$Think.lang.start}{$Think.lang.time}</th>
                        <th>{$Think.lang.end}{$Think.lang.time}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{:U('add')}">{$Think.lang.add}{$Think.lang.recruit}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="recruit_list" item="recruit">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$recruit.id}"/>
                                &nbsp;{$recruit.id}
                            </td>
                            <td>
                                {$recruit.title}
                            </td>
                            <td>
                                <if condition="$recruit['is_enable']">{$Think.lang.enable}<else/>{$Think.lang.disable}</if>
                            </td>
                            <td>
                                {$recruit.current_portion}/{$recruit.max_portion}
                            </td>
                            <td>
                                {$recruit.start_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                {$recruit.end_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['log_index']">
                                    <a class="btn btn-xs btn-primary" href="{:U('RecruitLog/index',array('r_id'=>$recruit['id']))}">
                                        {$Think.lang.look}{$Think.lang.re_recruit}
                                    </a>
                                </if>
                                <if condition="$batch_handle['log_index'] AND $batch_handle['edit']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['edit']">
                                    <a class="btn btn-xs btn-primary" href="{:U('edit',array('id'=>$recruit['id']))}">
                                        {$Think.lang.edit}
                                    </a>
                                </if>
                                <if condition="$batch_handle['edit'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}{$recruit.title}?','{:U('del',array('id'=>$recruit['id']))}')" >
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
                        <M:Page name="recruit_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>