    
    <import file="js/M_cate_tree" />
    <script type="text/javascript">
    $(function(){
        <foreach name="article_category_list" item="article_category">
            {//默认展开所有的子级分类,如果不展开提交时表单数据将不存在}
            <if condition="$article_category['checked']">
$('input[name="s_limit[{$article_category.id}]"]').val('{$edit_info['ext_info'][$article_category['id']]['s_limit']}' || 0);
$('select[name="template_list[{$article_category.id}]"] option[value="{$edit_info['ext_info'][$article_category['id']]['template']}"]').prop('selected',true);
$('select[name="list_template_list[{$article_category.id}]"] option[value="{$edit_info['ext_info'][$article_category['id']]['list_template']}"]').prop('selected',true);
$('select[name="article_template_list[{$article_category.id}]"] option[value="{$edit_info['ext_info'][$article_category['id']]['article_template']}"]').prop('selected',true);
M_cate_tree('input[name="category_list[]"][value="{$article_category.id}"]',article_channel_cb);
            </if>
        </foreach>
    });
    </script>
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.name}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{$Think.lang.channel}{$Think.lang.name}" name="name" value="{$edit_info.name}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.keywords}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{$Think.lang.channel}{$Think.lang.keywords}" name="keywords" value="{$edit_info.keywords}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.description}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{$Think.lang.channel}{$Think.lang.description}" name="description" value="{$edit_info.description}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.other}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{$Think.lang.channel}{$Think.lang.other}" name="other" value="{$edit_info.other}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.template}</label>
                                <div class="col-sm-6">
                                    <select name="template" class="form-control input-sm w200 fl" >
                                        <option value="">{$Think.lang.use}{$Think.lang.default}</option>
                                        <foreach name="channel_template_list" item="template">
                                            <option value="{$template.value}" <if condition="$template['value'] eq $edit_info['template']">selected="selected"</if> >{$template.name}</option>
                                        </foreach>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.yes}{$Think.lang.no}{$Think.lang.show}</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="1" <if condition="'1' heq $edit_info['if_show'] or !isset($edit_info['if_show'])">checked="checked"</if> />{$Think.lang.show}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="0" <if condition="'0' heq $edit_info['if_show']">checked="checked"</if> />{$Think.lang.hidden}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    {//是否可以管理权限}
                    <if condition="$manage_privilege">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.admin}</label>
                                <div class="col-sm-6"><h4 id="manage_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="admin_user_list">
                            <import file="js/M_select_add" />
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        <if condition="$edit_info['manage_id']">'def_data':{$edit_info.manage_id},</if>
                                        'out_obj':$('#manage_id_list'),
                                        'edit_obj':$('#admin_user_list'),
                                        'post_name':'manage_id[]',
                                        'ajax_url':'{:U('ajax_api')}',
                                        'field':'manage_id'
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.management}{$Think.lang.group}</label>
                                <div class="col-sm-6"><h4 id="manage_group_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="admin_group_list">
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        <if condition="$edit_info['manage_group_id']">'def_data':{$edit_info.manage_group_id},</if>
                                        'out_obj':$('#manage_group_id_list'),
                                        'edit_obj':$('#admin_group_list'),
                                        'post_name':'manage_group_id[]',
                                        'ajax_url':'{:U('ajax_api')}',
                                        'field':'manage_group_id'
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.access}{$Think.lang.member}{$Think.lang.group}</label>
                                <div class="col-sm-6"><h4 id="access_group_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="access_group_list">
                            <import file="js/M_select_add" />
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        <if condition="$edit_info['access_group_id']">'def_data':{$edit_info.access_group_id},</if>
                                        'out_obj':$('#access_group_id_list'),
                                        'edit_obj':$('#access_group_list'),
                                        'post_name':'access_group_id[]',
                                        'ajax_url':'{:U('ajax_api')}',
                                        'field':'access_group_id'
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    </if>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.channel}{$Think.lang.category}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-condensed table-hover">
                                <tr>
                                    <th></th>
                                    <th class="col-sm-1" >{$Think.lang.list}{$Think.lang.number}</th>
                                    <th class="col-sm-1" >{$Think.lang.category}{$Think.lang.template}</th>
                                    <th class="col-sm-1" >{$Think.lang.list}{$Think.lang.template}</th>
                                    <th class="col-sm-1" >{$Think.lang.article}{$Think.lang.template}</th>
                                </tr>
                                <foreach name="article_category_list" key="cate_key"  item="article_category">
                                    <tr cate_id="{$article_category.id}" parent_id="{$article_category.parent_id}" has_child="{$article_category.has_child}" >
                                        <td>
<span class="glyphicon <if condition="0 lt $article_category['has_child']">glyphicon-plus<else/>glyphicon-minus</if> mlr10" onclick="M_cate_tree(this,article_channel_cb);" ></span>
<input type="checkbox" name="category_list[]" value="{$article_category.id}" <if condition="$article_category['checked']">checked="checked"</if> onClick="M_cate_checkbox(this)" />
{$article_category.name}(ID:{$article_category.id})
                                        </td>
                                        <td <if condition="0 eq $cate_key">id="s_limit"</if>  >
                                            <input type="text" name="s_limit[{$article_category.id}]" style="width:100%;" onKeyup="M_in_int(this);" />
                                        </td>
                                        <td <if condition="0 eq $cate_key">id="template_list"</if> >
                                            <select name="template_list[{$article_category.id}]">
                                                <option value="">{$Think.lang.use}{$Think.lang.default}</option>
                                                <foreach name="template_list" item="template">
                                                    <option value="{$template.value}" >{$template.name}</option>
                                                </foreach>
                                            </select>
                                        </td>
                                        <td <if condition="0 eq $cate_key">id="list_template_list"</if> >
                                            <select name="list_template_list[{$article_category.id}]">
                                                <option value="">{$Think.lang.use}{$Think.lang.default}</option>
                                                <foreach name="list_template_list" item="template">
                                                    <option value="{$template.value}" >{$template.name}</option>
                                                </foreach>
                                            </select>
                                        </td>
                                        <td <if condition="0 eq $cate_key">id="article_template_list"</if> >
                                            <select name="article_template_list[{$article_category.id}]">
                                                <option value="">{$Think.lang.use}{$Think.lang.default}</option>
                                                <foreach name="article_template_list" item="template">
                                                    <option value="{$template.value}" >{$template.name}</option>
                                                </foreach>
                                            </select>
                                        </td>
                                    </tr>
                                </foreach>
                            </table>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">
                           <if condition="$Think.const.ACTION_NAME eq 'add'">
                               {$Think.lang.add}
                           <elseif condition="$Think.const.ACTION_NAME eq 'edit'" />
                               {$Think.lang.edit}
                           </if>
                        </button>
                       <a href="{:U('index')}" class="btn btn-default">
                           {$Think.lang.goback}
                       </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
