
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <include file="Public:where_info" />
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')" />&nbsp;{$Think.lang.id}</th>
                        <th>{$Think.lang.title}</th>
                        <th>{$Think.lang.sort}</th>
                        <th>{$Think.lang.channel}</th>
                        <th>{$Think.lang.category}</th>
                        <th>{$Think.lang.add}{$Think.lang.time}</th>
                        <th>{$Think.lang.show}</th>
                        <th>{$Think.lang.audit}</th>
                        <th>{$Think.lang.click}{$Think.lang.number}</th>
                        <td class="nowrap">
                            <if condition="$batch_handle['add']">
                                <a class="btn btn-xs btn-success" href="{:U('add')}">{$Think.lang.add}{$Think.lang.article}</a>
                            </if>
                        </td>
                    </tr>
                    <foreach name="article_list" item="article">
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{$article.id}"/>
                                &nbsp;{$article.id}
                            </td>
                            <td>
                                {$article.title}
                            </td>
                            <td onClick="M_line_edit(this);" field_id="{$article.id}" field="sort" link="{:U('ajax_api')}">
                                {$article.sort}
                            </td>
                            <td>
                                {$article.channel_name}
                            </td>
                            <td>
                                {$article.cate_name}
                            </td>
                            <td>
                                {$article.add_time|M_date=C('SYS_DATE_DETAIL')}
                            </td>
                            <td>
                                <if condition="$article['if_show']">{$Think.lang.yes}<else />{$Think.lang.no}</if>
                            </td>
                            <td>
                                <if condition="$article['is_audit']">{$Think.lang.yes}<else />{$Think.lang.no}</if>
                            </td>
                            <td>
                                {$article.hits}
                            </td>
                            <td class="nowrap">
                                <a class="btn btn-xs btn-primary" target="_blank" href="{:U('Home/Article/article',array('id'=>$article['id']))}">
                                    {$Think.lang.look}
                                </a>
                                <if condition="$batch_handle['edit']">
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-primary" href="{:U('edit',array('id'=>$article['id']))}">
                                        {$Think.lang.edit}
                                    </a>
                                </if>
                                <if condition="$batch_handle['del']">
                                    &nbsp;|&nbsp;
<a class="btn btn-xs btn-danger" href="javascript:void(0);" onClick="return M_confirm('{$Think.lang.confirm}{$Think.lang.del}{$article.title}?','{:U('del',array('id'=>$article['id']))}')" >
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
                                    config.type_data.push({'name':$Think.lang.show,'post_link':'{:U('edit')}','post_data':{'if_show':'1'} });
                                    config.type_data.push({'name':$Think.lang.hidden,'post_link':'{:U('edit')}','post_data':{'if_show':'0'} });
                                    config.type_data.push({'name':$Think.lang.audit,'post_link':'{:U('edit')}','post_data':{'is_audit':'1'} });
                                    config.type_data.push({'name':$Think.lang.cancel+$Think.lang.audit,'post_link':'{:U('edit')}','post_data':{'is_audit':'0'} });
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
                        <M:Page name="article_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>