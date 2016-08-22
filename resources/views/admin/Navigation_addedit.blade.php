
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <import file="js/M_valid" />
                <script>
                    $(function(){
                        var config = {
                            'form_obj':$('#form_valid'),
                            'check_list':{
                                'short_name':Array('short_name','id')
                            },
                            'ajax_url':"{:U('ajax_api')}",
                        };
                        new M_valid(config);
                    });
                </script>
                <form id="form_valid" onSubmit="return false;"  class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.navigation}{$Think.lang.name}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{$Think.lang.navigation}{$Think.lang.name}" name="name" value="{$edit_info.name}" onchange="M_zh2py(this,'input[name=short_name]')"  link="{:U('ajax_api')}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.short}{$Think.lang.name}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{$Think.lang.short}{$Think.lang.name}" name="short_name" value="{$edit_info.short_name}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.yes}{$Think.lang.no}{$Think.lang.enable}</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="1" <if condition="'1' heq $edit_info['is_enable'] or !isset($edit_info['is_enable'])">checked="checked"</if> />{$Think.lang.enable}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="0" <if condition="'0' heq $edit_info['is_enable']">checked="checked"</if> />{$Think.lang.disable}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <table id="edit_window" class="table table-condensed table-hover">
                            <tr>
                                <th class="col-sm-3">{$Think.lang.navigation}{$Think.lang.button}{$Think.lang.name}</th>
                                <th class="col-sm-1">{$Think.lang.target}{$Think.lang.type}</th>
                                <th class="col-sm-1">URL</th>
                                <th class="col-sm-7">
                                    {$Think.lang.insite}{$Think.lang.colon}M/C/A?arg1=argv1,arg2=argv2<br />
                                    {$Think.lang.outsite}{$Think.lang.colon}http:// | https:// | ftp://
                                </th>
                            </tr>
                            <tr>
                                <td><input class="form-control" type="text" name="nav_text" value="" style="width:100%" /></td>
                                <td>
                                    <select class="form-control" name="nav_target">
                                        <option value="_self">_self</option>
                                        <option value="_blank">_blank</option>
                                        <option value="_top">_top</option>
                                        <option value="_parent">_parent</option>
                                    </select>
                                </td>
                                <td colspan="2"><input class="form-control" type="text" name="nav_link" value="" style="width:100%" /></td>
                            </tr>
                        </table>
                        <div id="navigation_out" class="col-sm-12">
                            <import file="js/M_navigation_editor" />
                            <script>
                                $(function(){
                                    new M_navigation_editor({
                                            <if condition="$edit_info['ext_info']">'def_data':{$edit_info.ext_info},</if>
                                            'out_obj':$('#navigation_out'),
                                            'edit_obj':$('#edit_window'),
                                            'post_name':'{$navigation_config.post_name}',
                                            'max_level':{$navigation_config.navigation_level},
                                        });
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
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
                    </div>
                </form>
            </div>
        </div>
    </section>