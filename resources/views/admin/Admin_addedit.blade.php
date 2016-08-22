
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <import file="js/M_valid" />
                <script>
                    $(function(){
                        var config = {
                            'form_obj':$('#form_valid'),
                            'check_list':{
                                'admin_name':Array('admin_name','id'),
                                'password':Array('password','is_pwd'),
                                'password_again':Array('password','password_again','is_pwd')
                            },
                            'ajax_url':"{:U('ajax_api')}",
                        };
                        new M_valid(config);
                    });
                </script>
                <form id="form_valid" onSubmit="return false;" class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <input type="hidden" name="is_pwd" value="<if condition="$Think.const.ACTION_NAME eq 'add'">1<else />0</if>"/>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.admin') }}{{ trans('common.name') }}</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.admin') }}{{ trans('common.name') }}" name="admin_name" value="{$edit_info.admin_name}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.admin') }}{{ trans('common.pass') }}</label>
                                <div class="col-sm-3">
                                    <input type="password" class="form-control" placeholder="{{ trans('common.admin') }}{{ trans('common.pass') }}" name="password" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2  control-label">{{ trans('common.again') }}{{ trans('common.input') }}{{ trans('common.pass') }}</label>
                                <div class="col-sm-3">
                                    <input type="password" class="form-control" placeholder="{{ trans('common.again') }}{{ trans('common.input') }}{{ trans('common.pass') }}" name="password_again" />
                                    <if condition="$edit_info">
                                        <span class="help-block">{{ trans('common.not_input_pass') }}</span>
                                    </if>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.admin') }}{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.enable') }}</label>
                                <div class="col-sm-6">
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="1" <if condition="'1' heq $edit_info['is_enable'] or !isset($edit_info['is_enable'])">checked="checked"</if> />{{ trans('common.enable') }}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="0" <if condition="'0' heq $edit_info['is_enable']">checked="checked"</if> />{{ trans('common.disable') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.pertain') }}{{ trans('common.group') }}</label>
                                <div class="col-sm-6"><h4 id="group_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="group_list">
                            <import file="js/M_select_add" />
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        <if condition="$edit_info['group_id']">'def_data':{$edit_info.group_id},</if>
                                        'out_obj':$('#group_id_list'),
                                        'edit_obj':$('#group_list'),
                                        'post_name':'group_id[]',
                                        'ajax_url':'{:U('ajax_api')}',
                                        'field':'group_id'
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row mt10">
                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-info">
                                    <if condition="$Think.const.ACTION_NAME eq 'add'">
                                        {{ trans('common.add') }}
                                    <elseif condition="$Think.const.ACTION_NAME eq 'edit'" />
                                        {{ trans('common.edit') }}
                                    </if>
                                </button>
                                <a href="{:U('index')}" class="btn btn-default">
                                        {{ trans('common.goback') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{ trans('common.admin') }}{{ trans('common.privilege') }}</label>
                                <div class="col-sm-8 mb10">
                                    <label class="checkbox-inline"><input type="checkbox" onClick="M_allselect_par(this,'.row')" />{{ trans('common.allselect') }}</label>
                                    <input type="hidden" value="" name="privilege" />
                                </div>
                            </div>
                        </div>
                        <foreach name="privilege" key="controller" item="privs">
                        <div class="col-sm-12">
                            <ul class="list-group">
                                <li class="list-group-item list-group-item-info">
                                    <label class="checkbox-inline"><input type="checkbox" onClick="M_allselect_par(this,'ul')" />{{ trans('common.allselect') }}{{ $controller }}</label>
                                </li>
                                <foreach name="privs" key="controller_name" item="actions">
                                    <li class="list-group-item">
                                    <foreach name="actions" key="action_name" item="action_value">
                                        <label class="checkbox-inline">
<input type="checkbox" name="privilege[]" value="{{ $controller_name }}_{{ $action_name }}"
    <if condition="'all' eq $edit_info['privilege'] or (is_array($edit_info['privilege']) AND in_array($controller_name.'_'.$action_name,$edit_info['privilege']))">
        checked="checked"
    </if>
/>
                                            {{ $action_value }}
                                        </label>
                                    </foreach>
                                    </li>
                                </foreach>
                            </ul>
                        </div>
                        </foreach>
                    </div>
                </form>
            </div>
        </div>
    </section>
