    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <form method="post" class="form-horizontal"  role="form">
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    {$Think.lang.messageboard}{$Think.lang.name}
                                </label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="name" value="{$edit_info.name}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.messageboard}{$Think.lang.template}</label>
                                <div class="col-sm-6">
                                    <select name="template" class="form-control input-sm" >
                                        <option value="">{$Think.lang.use}{$Think.lang.default}</option>
                                        <foreach name="template_list" item="template">
                                            <option value="{$template.value}" <if condition="$template['value'] eq $edit_info['template']">selected="selected"</if> >{$template.name}</option>
                                        </foreach>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-hover">
                        <tr>
                            <th class="col-sm-6 text-center">{$Think.lang.submit}{$Think.lang.option}</th>
                            <th class="col-sm-2 text-center">{$Think.lang.submit}{$Think.lang.type}</th>
                            <th class="col-sm-4 text-center">{$Think.lang.option}{$Think.lang.condition}</th>
                            <th id="btn_obj" class="col-sm-2 text-center"></th>
                        </tr>
                        <tr id="edit_obj" style="display: none">
                            <td class="col-sm-6">
                                <input class="form-control" mtype="msg_name" />
                            </td>
                            <td class="col-sm-2">
                                <select class="form-control" mtype="msg_type">
                                    <option value="text" >{$Think.lang.text}</option>
                                    <option value="radio" >{$Think.lang.radio}(name:var1,var2)</option>
                                    <option value="checkbox" >{$Think.lang.checkbox}(name:var1,var2)</option>
                                    <option value="textarea" >{$Think.lang.textarea}</option>
                                </select>
                            </td>
                            <td class="col-sm-4" colspan="2">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label checkbox checkbox-inline">
                                        <input type="checkbox" value="required" mtype="msg_required" />{$Think.lang.required}
                                    </label>
                                    <label class="col-sm-3 control-label" style="font-weight:normal;">
                                        {$Think.lang.max}{$Think.lang.length}
                                    </label>
                                    <div class="col-sm-3">
                                        <input class="form-control" type="text" value="" onKeyup="M_in_int(this);" mtype="msg_length" />
                                    </div>
                                    <a class="btn btn-danger" href="javascript:void(0);">{$Think.lang.del}</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table id="out_obj" class="table table-hover">
                        <import file="js/M_messageboard_editor" />
                        <script>
                            $(function(){
                                new M_messageboard_editor({
                                        <if condition="$edit_info['config']">'def_data':{$edit_info.config},</if>
                                        'out_obj':$('#out_obj'),
                                        'edit_obj':$('#edit_obj'),
                                        'btn_obj':$('#btn_obj'),
                                        'post_name':'config'
                                    });
                            });
                        </script>
                    </table>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">{$Think.lang.save}</button>
                            <input class="btn btn-default" type="reset" value="{$Think.lang.reset}">
                            <a href="{:U('main')}" class="btn btn-default">
                                {$Think.lang.goback}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>