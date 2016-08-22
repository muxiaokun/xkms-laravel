
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.region_name}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{$Think.lang.region_name}" name="region_name" value="{$edit_info.region_name}" onchange="M_zh2py(this,'input[name=short_name]')"  link="{:U('ajax_api')}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.short_name}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{$Think.lang.short_name}" name="short_name" value="{$edit_info.short_name}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.all_spell}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{$Think.lang.all_spell}" name="all_spell" value="{$edit_info.all_spell}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.short_spell}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{$Think.lang.short_spell}" name="short_spell" value="{$edit_info.short_spell}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.areacode}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{$Think.lang.areacode}" name="areacode" value="{$edit_info.areacode}" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.postcode}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{$Think.lang.postcode}" name="postcode" value="{$edit_info.postcode}" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.parent_level}</label>
                                <div class="col-sm-6" id="region_list">
                                    <input type="hidden" name="parent_id"/>
                                    <import file="js/M_select_add" />
                                    <script type="text/javascript">
                                        $(function(){
                                            var config = {
                                                <if condition="$edit_info['parent_id']">'def_data':{'value':{$edit_info.parent_id},'html':'{$edit_info.parent_name}'},</if>
                                                'edit_obj':$('#region_list'),
                                                'post_name':'parent_id',
                                                'ajax_url':'{:U('ajax_api')}',
                                                'field':'parent_id'
                                            };
                                            new M_select_add(config);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{$Think.lang.yes}{$Think.lang.no}{$Think.lang.show}</label>
                                <div class="col-sm-4">
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="1" <if condition="'1' heq $edit_info['if_show']">checked="checked"</if> />{$Think.lang.show}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="0" <if condition="'0' heq $edit_info['if_show'] or !isset($edit_info['if_show'])">checked="checked"</if> />{$Think.lang.hidden}
                                    </label>
                                </div>
                            </div>
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