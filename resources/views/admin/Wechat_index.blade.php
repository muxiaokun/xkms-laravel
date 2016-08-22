
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.member}{$Think.lang.name}</th>
                        <th>{$Think.lang.headimg}</th>
                        <th>{$Think.lang.nickname}</th>
                        <th>{$Think.lang.sex}</th>
                        <th>{$Think.lang.address}[{$Think.lang.language}]</th>
                        <th>{$Think.lang.bind}{$Think.lang.time}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{:U('add')}">{$Think.lang.config}{$Think.lang.wechat}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="wechat_list" item="wechat">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$wechat.id}"/>
                                &nbsp;{$wechat.id}
                            </td>
                            <td>
                               {$wechat.member_name}
                            </td>
                            <td>
                                <a href="{$wechat.headimgurl}" target="_blank">{$Think.lang.look}</a>
                            </td>
                            <td>
                                {$wechat.nickname}
                            </td>
                            <td>
                                {$wechat.sex}
                            </td>
                            <td>
                                {$wechat.country}{$wechat.province}{$wechat.city}[{$wechat.language}]
                            </td>
                            <td>
                                {$wechat.bind_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['edit']">
                                    <a class="btn btn-xs btn-primary"  href="{:U('edit',array('id'=>$wechat['id']))}">
                                        {$Think.lang.send}{$Think.lang.info}
                                    </a>
                                </if>
                                <if condition="$batch_handle['edit'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}{$wechat.member_name}?','{:U('del',array('id'=>$wechat['id']))}')" >
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
                                <if condition="$batch_handle['del']">
                                    config.type_data.push({'name':$Think.lang.del,'post_link':'{:U('del')}' });
                                </if>
                                new M_batch_handle(config);
                            });
                        </script>
                        </if>
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="wechat_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
