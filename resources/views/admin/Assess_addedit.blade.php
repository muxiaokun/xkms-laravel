    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.assess') }}{{ trans('common.title') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.title') }}" name="title" value="{$edit_info.title}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.assess') }}{{ trans('common.group') }}</label>
                                <div class="col-sm-8" id="group_level">
                                    <input type="hidden" name="group_level" />
                                    <import file="js/M_select_add" />
                                    <script type="text/javascript">
                                        $(function(){
                                            var config = {
                                                <if condition="$edit_info['group_level']">'def_data':{'value':'{$edit_info.group_level}','html':'{$edit_info.group_name}'},</if>
                                                'edit_obj':$('#group_level'),
                                                'post_name':'group_level',
                                                'ajax_url':'{{ route('ajax_api') }}',
                                                'field':'group_level'
                                            };
                                            new M_select_add(config);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.assess') }}{{ trans('common.start') }}{{ trans('common.time') }}</label>
                                <div class="col-sm-6 ">
<input type="text" class="form-control" placeholder="{{ trans('common.start') }}{{ trans('common.time') }}" name="start_time" value="{$edit_info.start_time|M_date=C('SYS_DATE_DETAIL')}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.assess') }}{{ trans('common.end') }}{{ trans('common.time') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{{ trans('common.end') }}{{ trans('common.time') }}" name="end_time" value="{$edit_info.end_time|M_date=C('SYS_DATE_DETAIL')}"/>
                                </div>
                            </div>
                        </div>
                        <M:Timepicker start="start_time" end="end_time" />
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.assess') }}{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.enable') }}</label>
                                <div class="col-sm-6">
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="1" <if condition="'1' heq $edit_info['is_enable'] or !isset($edit_info['is_enable'])" >checked="checked"</if> />{{ trans('common.enable') }}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="0" <if condition="'0' heq $edit_info['is_enable']">checked="checked"</if> />{{ trans('common.disable') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.assess') }}{{ trans('common.target') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control input-sm w260" name="target">
<option value="member"  <if condition="'member' eq $edit_info['target']">selected="selected"</if> >{{ trans('common.member') }}</option>
<option value="member_group"  <if condition="'member_group' eq  $edit_info['target']">selected="selected"</if> >{{ trans('common.member') }}{{ trans('common.group') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.assess') }}{{ trans('common.explains') }}</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="3" style="width:100%;resize:none;" name="explains" >{$edit_info.explains}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                <if condition="$Think.const.ACTION_NAME eq 'add'">
                                    {{ trans('common.add') }}
                                <elseif condition="$Think.const.ACTION_NAME eq 'edit'" />
                                    {{ trans('common.edit') }}
                                </if>
                            </button>
                            <a href="{{ route('index') }}" class="btn btn-default">
                                    {{ trans('common.goback') }}
                            </a>
                        </div>
                    </div>
                    <div class="row"  id="quests_edit">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.project') }}</label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" mtype="p"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.factor') }}</label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" mtype="f"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.max') }}{{ trans('common.minute') }}{{ trans('common.number') }}</label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" mtype="mg" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.max') }}{{ trans('common.minute') }}{{ trans('common.number') }}</label>
                                <div class="col-sm-6">
                                    <a class="btn btn-default" href="javascript:void(0);" mtype="add_assess" >{{ trans('common.add') }}{{ trans('common.project') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ trans('common.project') }}</th>
                                        <th>{{ trans('common.factor') }}</th>
                                        <th>{{ trans('common.max') }}{{ trans('common.grade') }}</th>
                                        <th class="col-sm-2"></th>
                                    </tr>
                                </thead>
                                <tbody id="assess_area">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <import file="js/M_assess_editor" />
                    <script type="text/javascript">
                        var config = {
                            <if condition="$edit_info['ext_info']">'def_data':{$edit_info.ext_info},</if>
                            'out_obj':$('#assess_area'),
                            'edit_obj':$('#quests_edit'),
                            'post_name':'ext_info'
                        }
                        new M_assess_editor(config);
                    </script>
                </form>
            </div>
        </div>
    </section>