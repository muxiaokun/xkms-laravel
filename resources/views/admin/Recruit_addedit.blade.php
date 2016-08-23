
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form  class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.recruit') }}{{ trans('common.name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.recruit') }}{{ trans('common.name') }}" name="title" value="{$edit_info.title}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.current') }}{{ trans('common.recruit') }}{{ trans('common.number') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="current_portion" value="{$edit_info.current_portion}" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.max') }}{{ trans('common.recruit') }}{{ trans('common.number') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="max_portion" value="{$edit_info.max_portion}" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.start') }}{{ trans('common.time') }}</label>
                                <div class="col-sm-6 ">
<input type="text" class="form-control" placeholder="{{ trans('common.start') }}{{ trans('common.time') }}" name="start_time" value="{$edit_info.start_time|M_date=C('SYS_DATE_DETAIL')}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.end') }}{{ trans('common.time') }}</label>
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
                                <label class="col-sm-4 control-label">{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.enable') }}</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="1" <if condition="'1' heq $edit_info['is_enable'] or !isset($edit_info['is_enable'])">checked="checked"</if> />{{ trans('common.enable') }}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="is_enable" value="0" <if condition="'0' heq $edit_info['is_enable']">checked="checked"</if> />{{ trans('common.disable') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div id="edit_obj" class="form-group">
                                <div class="col-sm-4 text-right">
                                    <button type="button" class="btn btn-default" mtype="in_add" >{{ trans('common.add') }}{{ trans('common.extend') }}{{ trans('common.info') }}</button>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" mtype="in_val" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="ext_info_list" class="row">
                        <import file="js/M_exttpl_editor" />
                        <script type="text/javascript">
                            $(function(){
                                var config = {
                                    <if condition="$edit_info['ext_info']">'def_data':{$edit_info.ext_info|json_encode},</if>
                                    'run_type':'add_edit',
                                    'out_obj':$('#ext_info_list'),
                                    'edit_obj':$('#edit_obj'),
                                    'post_name':'ext_info'
                                };
                                new M_exttpl_editor(config);
                            });
                        </script>
                    </div>
                    <div class="col-sm-12">
                        <label class="col-sm-12">{{ trans('common.recruit') }}{{ trans('common.content') }}</label>
                        <textarea rows="15" class="col-sm-12" name="explains">{$edit_info.explains}</textarea>
                    </div>
                    <div class="cb"></div>
                    <div class="row mt10">
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
                </form>
            </div>
        </div>
    </section>
    <M:Kindeditor name="explains" />