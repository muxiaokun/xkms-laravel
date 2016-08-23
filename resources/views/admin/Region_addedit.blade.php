
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{$edit_info.id}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.region_name') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{{ trans('common.region_name') }}" name="region_name" value="{$edit_info.region_name}" onchange="M_zh2py(this,'input[name=short_name]')"  link="{{ route('ajax_api') }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.short_name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.short_name') }}" name="short_name" value="{$edit_info.short_name}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.all_spell') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.all_spell') }}" name="all_spell" value="{$edit_info.all_spell}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.short_spell') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.short_spell') }}" name="short_spell" value="{$edit_info.short_spell}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.areacode') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{{ trans('common.areacode') }}" name="areacode" value="{$edit_info.areacode}" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.postcode') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{{ trans('common.postcode') }}" name="postcode" value="{$edit_info.postcode}" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.parent_level') }}</label>
                                <div class="col-sm-6" id="region_list">
                                    <input type="hidden" name="parent_id"/>
                                    <import file="js/M_select_add" />
                                    <script type="text/javascript">
                                        $(function(){
                                            var config = {
                                                <if condition="$edit_info['parent_id']">'def_data':{'value':{$edit_info.parent_id},'html':'{$edit_info.parent_name}'},</if>
                                                'edit_obj':$('#region_list'),
                                                'post_name':'parent_id',
                                                'ajax_url':'{{ route('ajax_api') }}',
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
                                <label class="col-sm-4 control-label">{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.show') }}</label>
                                <div class="col-sm-4">
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="1" <if condition="'1' heq $edit_info['if_show']">checked="checked"</if> />{{ trans('common.show') }}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="0" <if condition="'0' heq $edit_info['if_show'] or !isset($edit_info['if_show'])">checked="checked"</if> />{{ trans('common.hidden') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
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