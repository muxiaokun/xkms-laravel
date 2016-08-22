
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.itlink}{$Think.lang.name}</th>
                        <th>{$Think.lang.short}{$Think.lang.name}</th>
                        <th>{$Think.lang.yes}{$Think.lang.no}{$Think.lang.enable}</th>
                        <th>{$Think.lang.yes}{$Think.lang.no}{$Think.lang.statistics}</th>
                        <th>{$Think.lang.show}{$Think.lang.statistics}</th>
                        <th>{$Think.lang.click}{$Think.lang.statistics}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['edit']">
                                <a class="btn btn-xs btn-success"  href="{:U('add')}">{$Think.lang.add}{$Think.lang.itlink}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="itlink_list" item="itlink">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$itlink.id}"/>
                                &nbsp;{$itlink.id}
                            </td>
                            <td>
                                {$itlink.name}
                            </td>
                            <td>
                                {$itlink.short_name}
                            </td>
                            <td>
                                <if condition="$itlink['is_enable']">{$Think.lang.yes}<else />{$Think.lang.no}</if>
                            </td>
                            <td>
                                <if condition="$itlink['is_statistics']">{$Think.lang.yes}<else />{$Think.lang.no}</if>
                            </td>
                            <td>
                                {$itlink.show_num}
                            </td>
                            <td>
                                {$itlink.hit_num}
                            </td>
                            <td class="nowrap">
                                <if condition="$batch_handle['edit']">
                                    <a class="btn btn-xs btn-primary"  href="{:U('edit',array('id'=>$itlink['id']))}">
                                        {$Think.lang.edit}
                                    </a>
                                </if>
                                <if condition="$batch_handle['edit'] AND $batch_handle['del']">&nbsp;|&nbsp;</if>
                                <if condition="$batch_handle['del']">
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}{$itlink.name}?','{:U('del',array('id'=>$itlink['id']))}')" >
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
                                    config.type_data.push({'name':$Think.lang.statistics,'post_link':'{:U('edit')}','post_data':{'is_statistics':'1'} });
                                    config.type_data.push({'name':$Think.lang.cancel+$Think.lang.statistics,'post_link':'{:U('edit')}','post_data':{'is_statistics':'0'} });
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
                        <M:Page name="itlink_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>