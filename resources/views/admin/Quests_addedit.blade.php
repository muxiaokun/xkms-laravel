
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.title') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.title') }}" name="title" value="{$edit_info.title}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.max') }}{{ trans('common.portion') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" onKeyup="M_in_int(this);" placeholder="{{ trans('common.max') }}{{ trans('common.portion') }}" name="max_portion" value="{$edit_info.max_portion}"/>
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
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.access') }}{{ trans('common.pass') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.quests_note1') }}" name="access_info" value="{$edit_info.access_info}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.start') }}{{ trans('common.content') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="start_content" value="{$edit_info.start_content}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2  control-label">{{ trans('common.end') }}{{ trans('common.content') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="end_content" value="{$edit_info.end_content}" />
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <div class="cb"></div>
                    <div id="quests_edit" class="row mt10">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.question') }}</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" disabled="disabled" mtype="question" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.explains') }}</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" disabled="disabled" mtype="explains" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.answer') }}</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" placeholder="{{ trans('common.quests_quest_note1') }}" disabled="disabled" mtype="answer" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.answer') }}{{ trans('common.type') }}</label>
                                <div class="checkbox col-sm-10">
                                    <label>
                                        <input type="radio" name="answer_type" value="radio" disabled="disabled" checked="checked" />{{ trans('common.radio') }}
                                    </label>
                                    <label>
                                        <input type="radio" name="answer_type" value="checkbox" disabled="disabled"/>{{ trans('common.checkbox') }}
                                    </label>
                                    <label>
                                        <input type="radio" name="answer_type" value="text" disabled="disabled"/>{{ trans('common.text') }}
                                    </label>
                                    <label>
                                        <input type="radio" name="answer_type" value="textarea" disabled="disabled"/>{{ trans('common.textarea') }}
                                    </label>
                                    <label class="ml20">
                                        <input type="checkbox" mtype="required" disabled="disabled" />
                                        <span style="color:red;">{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.required') }}</span>
                                    </label>
                                    <label class="ml20">
                                        <button type="button" mtype="add_question">{{ trans('common.add') }}{{ trans('common.question') }}</button>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="quests_area" class="col-sm-offset-2 col-sm-9 list-group">
                        </div>
                    </div>
                    <import file="js/M_quests_editor" />
                    <script type="text/javascript">
                        $(function(){
                            var config = {
                                <if condition="$edit_info['ext_info']">'def_data':{$edit_info.ext_info},</if>
                                'out_obj':$('#quests_area'),
                                'edit_obj':$('#quests_edit'),
                                'post_name':'ext_info'
                            }
                            new M_quest_editor(config);
                        });
                    </script>
                </form>
            </div>
        </div>
    </section>