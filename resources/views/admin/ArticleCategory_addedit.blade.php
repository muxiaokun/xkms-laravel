    
        <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
        <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post" >
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.category') }}{{ trans('common.name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.category') }}{{ trans('common.name') }}" name="name" value="{{ $edit_info['name'] }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.sort') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.sort') }}" name="sort" value="{{ $edit_info['sort'] }}" onKeyup="M_in_int(this);" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.parent_level') }}</label>
                                <div class="col-sm-6">
                                    <select name="parent_id" class="form-control input-sm" >
                                        <option value="0">{{ trans('common.top_level') }}{{ trans('common.enable') }}{{ trans('common.attribute') }}/{{ trans('common.extend') }}</option>
                                        @foreach ($category_list as $category)
                                            <option value="{{ $category['id'] }}" @if ($category['id'] eq $edit_info['parent_id'])selected="selected"@endif >{{ $category['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <script type="text/javascript">
                                        $(function(){
                                            function check_cate_id(obj)
                                            {
                                                var top_cate_col = $('#template_list,#attribute').parents('.row');
                                                if(0 == obj.val())
                                                {
                                                    top_cate_col.show();
                                                }
                                                else
                                                {
                                                    top_cate_col.hide();
                                                }
                                            }
                                            var select_parent_id = $('select[name=parent_id]');
                                            $('select[name=parent_id]').on('change',function(){check_cate_id(select_parent_id);});
                                            check_cate_id(select_parent_id);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.show') }}</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="1" @if ('1' heq $edit_info['if_show'] or !isset($edit_info['if_show']))checked="checked"@endif />{{ trans('common.show') }}
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="if_show" value="0" @if ('0' heq $edit_info['if_show'])checked="checked"@endif />{{ trans('common.hidden') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.content') }}{{ trans('common.template') }}</label>
                                <div class="col-sm-6">
                                    <select name="template" class="form-control input-sm" >
                                        <option value="">{{ trans('common.use') }}{{ trans('common.default') }}</option>
                                        @foreach ($template_list as $template)
                                            <option value="{{ $template['value'] }}" @if ($template['value'] eq $edit_info['template'])selected="selected"@endif >{{ $template['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.article') }}{{ trans('common.template') }}</label>
                                <div class="col-sm-6">
                                    <select name="article_template" class="form-control input-sm" >
                                        <option value="">{{ trans('common.use') }}{{ trans('common.default') }}</option>
                                        @foreach ($article_template_list as $template)
                                            <option value="{{ $template['value'] }}" @if ($template['value'] eq $edit_info['article_template'])selected="selected"@endif >{{ $template['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.list') }}{{ trans('common.template') }}</label>
                                <div class="col-sm-6">
                                    <select name="list_template" class="form-control input-sm" >
                                        <option value="">{{ trans('common.use') }}{{ trans('common.default') }}</option>
                                        @foreach ($list_template_list as $template)
                                            <option value="{{ $template['value'] }}" @if ($template['value'] eq $edit_info['list_template'])selected="selected"@endif >{{ $template['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.list') }}{{ trans('common.number') }}</label>
                                <div class="col-sm-6">
<input type="text" class="form-control" placeholder="{{ trans('common.list') }}{{ trans('common.number') }}" name="s_limit" value="{{ $edit_info['s_limit'] }}" onKeyup="M_in_int(this);" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.thumb') }}</label>
                                <div class="col-sm-5">
                                    <div id="uploadbutton" title="{{ trans('common.selection') }}{{ trans('common.or') }}{{ trans('common.upload') }}" class="default_image">
                                        <M:Img id="thumb_src" src="{:M_exists($edit_info['thumb'])}" />
                                    </div>
                                    <input id="thumb_val" type="hidden" name="thumb" value="{{ $edit_info['thumb'] }}" />
                                    <script>
                                        function M_article_uploadbutton(url,title)
                                        {
                                            {//必须替换掉__ROOT__/ 原因伪静态目录结构}
                                            $('#thumb_val').val(url.replace(RegExp('^'+$Think.root),''));
                                            $('#thumb_src').attr('src',url);
                                        }
                                        M_jqueryui_tooltip('#uploadbutton');
                                    </script>
                                    <M:Uploadfile id="uploadbutton" type="image" dir="kindeditor" cb_fn="M_article_uploadbutton" />
                                </div>
                            </div>
                        </div>
                    </div>
                    {//是否可以管理权限}
                    @if ($manage_privilege)
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.admin') }}</label>
                                <div class="col-sm-6"><h4 id="manage_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="admin_user_list">
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        @if ($edit_info['manage_id'])'def_data':{{ $edit_info['manage_id'] }},@endif
                                        'out_obj':$('#manage_id_list'),
                                        'edit_obj':$('#admin_user_list'),
                                        'post_name':'manage_id[]',
                                        'ajax_url':'{{ route('ajax_api') }}',
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
                                <label class="col-sm-4 control-label">{{ trans('common.management') }}{{ trans('common.group') }}</label>
                                <div class="col-sm-6"><h4 id="manage_group_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="admin_group_list">
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        @if ($edit_info['manage_group_id'])'def_data':{{ $edit_info['manage_group_id'] }},@endif
                                        'out_obj':$('#manage_group_id_list'),
                                        'edit_obj':$('#admin_group_list'),
                                        'post_name':'manage_group_id[]',
                                        'ajax_url':'{{ route('ajax_api') }}',
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
                                <label class="col-sm-4 control-label">{{ trans('common.access') }}{{ trans('common.member') }}{{ trans('common.group') }}</label>
                                <div class="col-sm-6"><h4 id="access_group_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="access_group_list">
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        @if ($edit_info['access_group_id'])'def_data':{{ $edit_info['access_group_id'] }},@endif
                                        'out_obj':$('#access_group_id_list'),
                                        'edit_obj':$('#access_group_list'),
                                        'post_name':'access_group_id[]',
                                        'ajax_url':'{{ route('ajax_api') }}',
                                        'field':'access_group_id'
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ trans('common.other') }}{{ trans('common.info') }}{{ trans('common.template') }}</label>
                                <div class="col-sm-6"><ul id="template_list" class="list-group"></ul></div>
                            </div>
                        </div>
                        <div id="template_edit" class="col-sm-6">
                            <script type="text/javascript" src="{{ asset('js/M_exttpl_editor.js') }}"></script>
                            <script type="text/javascript">
                                $(function(){
                                    var config = {
                                        @if ($edit_info['extend'])'def_data':{{ $edit_info['extend']|json_encode }},@endif
                                        'run_type':'add',
                                        'out_obj':$('#template_list'),
                                        'edit_obj':$('#template_edit'),
                                        'post_name':'extend[]'
                                    };
                                    new M_exttpl_editor(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.attribute') }}{{ trans('common.template') }}</label>
                                <div id="attribute" class="col-sm-8" >
                                    <script type="text/javascript" src="{{ asset('js/M_attribute_editor.js') }}"></script>
                                    <script type="text/javascript">
                                        $(function(){
                                            var config = {
                                                @if ($edit_info['attribute'])'def_data':{{ $edit_info['attribute']|json_encode }},@endif
                                                'run_type':'edit',
                                                'out_obj':$('#attribute'),
                                                'post_name':'attribute'
                                            };
                                            new M_attribute_editor(config);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('common.enable') }}{{ trans('common.category') }}{{ trans('common.content') }}</label>
                                <div class="col-sm-10">
                                    <label class="radio-inline">
<input type="radio" name="is_content" value="1" @if ('1' heq $edit_info['is_content'])checked="checked"@endif />
                                        {{ trans('common.enable') }}({{ trans('common.content') }}{{ trans('common.template') }})
                                    </label>
                                    <label class="radio-inline">
<input type="radio" name="is_content" value="0" @if ('0' heq $edit_info['is_content'] or !isset($edit_info['is_content']))checked="checked"@endif />
                                        {{ trans('common.disable') }}({{ trans('common.list') }}{{ trans('common.template') }})
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label class="col-sm-12">{{ trans('common.category') }}{{ trans('common.content') }}</label>
                        <textarea rows="15" class="col-sm-12" name="content">{{ $edit_info['content'] }}</textarea>
                    </div>
                    <div class="cb"></div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                @if ($Think.const.ACTION_NAME eq 'add')
                                    {{ trans('common.add') }}
                                @elseif ($Think.const.ACTION_NAME eq 'edit')
                                    {{ trans('common.edit') }}
                                @endif
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
    <M:Kindeditor name="content" />
