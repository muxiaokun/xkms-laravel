@extends('admin.layout')
@section('body')
    <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.category')@lang('common.name')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.category')@lang('common.name')" name="name"
                                           value="{{ $edit_info['name'] }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.sort')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="@lang('common.sort')"
                                           name="sort"
                                           onKeyup="M_in_int_range(this,1,100);"
                                           value="@if ($edit_info['sort']){{ $edit_info['sort'] }}@else 100 @endif"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.list')@lang('common.number')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.list')@lang('common.number')" name="s_limit"
                                           onKeyup="M_in_int(this);"
                                           value="@if ($edit_info['s_limit']){{ $edit_info['s_limit'] }}@else 0 @endif"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.yes')@lang('common.no')@lang('common.show')</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="if_show" value="1"
                                               @if (1 === $edit_info['if_show'] or '' === $edit_info['if_show'])checked="checked"@endif />@lang('common.show')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="if_show" value="0"
                                               @if (0 === $edit_info['if_show'])checked="checked"@endif />@lang('common.hidden')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.content')@lang('common.template')</label>
                                <div class="col-sm-6">
                                    <select name="template" class="form-control input-sm">
                                        <option value="">@lang('common.use')@lang('common.default')</option>
                                        @foreach ($template_list as $template)
                                            <option value="{{ $template['value'] }}"
                                                    @if ($template['value'] == $edit_info['template'])selected="selected"@endif >{{ $template['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.article')@lang('common.template')</label>
                                <div class="col-sm-6">
                                    <select name="article_template" class="form-control input-sm">
                                        <option value="">@lang('common.use')@lang('common.default')</option>
                                        @foreach ($article_template_list as $template)
                                            <option value="{{ $template['value'] }}"
                                                    @if ($template['value'] == $edit_info['article_template'])selected="selected"@endif >{{ $template['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.list')@lang('common.template')</label>
                                <div class="col-sm-6">
                                    <select name="list_template" class="form-control input-sm">
                                        <option value="">@lang('common.use')@lang('common.default')</option>
                                        @foreach ($list_template_list as $template)
                                            <option value="{{ $template['value'] }}"
                                                    @if ($template['value'] == $edit_info['list_template'])selected="selected"@endif >{{ $template['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.thumb')</label>
                                <div class="col-sm-5">
                                    <div id="uploadbutton"
                                         title="@lang('common.selection')@lang('common.or')@lang('common.upload')"
                                         class="default_image">
                                        @asyncImg(<img id="thumb_src" src="{{ mExists($edit_info['thumb']) }}"/>)
                                    </div>
                                    <input id="thumb_val" type="hidden" name="thumb" value="{{ $edit_info['thumb'] }}"/>
                                    <script>
                                        function M_article_uploadbutton(url, title) {
                                            $('#thumb_val').val(url);
                                            $('#thumb_src').attr('src', url);
                                        }
                                        M_jqueryui_tooltip('#uploadbutton');
                                    </script>
                                    @uploadfile(uploadbutton,image,kindeditor,M_article_uploadbutton)
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- 是否可以管理权限 --}}
                    @if ($manage_privilege)
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">@lang('common.admin')</label>
                                    <div class="col-sm-6"><h4 id="manage_id_list" style="margin:2px 0px 0px 0px;"></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6" id="admin_user_list">
                                <script type="text/javascript">
                                    $(function () {
                                        var config = {
                                            @if ($edit_info['manage_id'])'def_data':{!! json_encode($edit_info['manage_id']) !!},
                                            @endif
                                            'out_obj': $('#manage_id_list'),
                                            'edit_obj': $('#admin_user_list'),
                                            'post_name': 'manage_id[]',
                                            'ajax_url': '{{ route('Admin::ArticleCategory::ajax_api') }}',
                                            'field': 'manage_id'
                                        };
                                        new M_select_add(config);
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">@lang('common.management')@lang('common.group')</label>
                                    <div class="col-sm-6"><h4 id="manage_group_id_list"
                                                              style="margin:2px 0px 0px 0px;"></h4></div>
                                </div>
                            </div>
                            <div class="col-sm-6" id="admin_group_list">
                                <script type="text/javascript">
                                    $(function () {
                                        var config = {
                                            @if ($edit_info['manage_group_id'])'def_data':{!! json_encode($edit_info['manage_group_id']) !!},
                                            @endif
                                            'out_obj': $('#manage_group_id_list'),
                                            'edit_obj': $('#admin_group_list'),
                                            'post_name': 'manage_group_id[]',
                                            'ajax_url': '{{ route('Admin::ArticleCategory::ajax_api') }}',
                                            'field': 'manage_group_id'
                                        };
                                        new M_select_add(config);
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">@lang('common.access')@lang('common.member')@lang('common.group')</label>
                                    <div class="col-sm-6"><h4 id="access_group_id_list"
                                                              style="margin:2px 0px 0px 0px;"></h4></div>
                                </div>
                            </div>
                            <div class="col-sm-6" id="access_group_list">
                                <script type="text/javascript">
                                    $(function () {
                                        var config = {
                                            @if ($edit_info['access_group_id'])'def_data':{!! json_encode($edit_info['access_group_id']) !!},
                                            @endif
                                            'out_obj': $('#access_group_id_list'),
                                            'edit_obj': $('#access_group_list'),
                                            'post_name': 'access_group_id[]',
                                            'ajax_url': '{{ route('Admin::ArticleCategory::ajax_api') }}',
                                            'field': 'access_group_id'
                                        };
                                        new M_select_add(config);
                                    });
                                </script>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.parent_level')</label>
                                <div id="parent_id" class="col-sm-9">
                                    @if (isset($edit_info['category_tree']) && !$edit_info['category_tree']->isEmpty())
                                        <input type="hidden" name="parent_id" value="{{ $edit_info['parent_id'] }}"/>
                                        @foreach($edit_info['category_tree'] as $key => $categorys)
                                            <select class="form-control w180 fl">
                                                @if (0 === $key)
                                                    <option value="">@lang('common.top_level')@lang('common.enable')@lang('common.attribute')
                                                        /@lang('common.extend')</option>
                                                @else
                                                    <option value="">@lang('common.please')@lang('common.selection')</option>
                                                @endif
                                                @foreach($categorys['category_list'] as $category)
                                                    <option @if ($categorys['id'] == $category['id'])selected="selected"
                                                            @endif
                                                            value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                                @endforeach
                                            </select>
                                        @endforeach
                                    @endif
                                </div>
                                <script type="text/javascript"
                                        src="{{ asset('js/M_multilevel_selection.js') }}"></script>
                                <script type="text/javascript">
                                    $(function () {
                                        var config = {
                                            'out_obj': $('#parent_id'),
                                            'submit_type': 'id',
                                            'edit_obj': $('<select class="form-control w100 fl"></select>'),
                                            'post_name': 'parent_id',
                                            'ajax_url': '{{ route('Admin::ArticleCategory::ajax_api',['inserted'=>$edit_info['parent_id']]) }}'
                                        };
                                        new M_multilevel_selection(config);

                                        function check_cate_id(obj) {
                                            var top_cate_col = $('#extend,#attribute').parents('.row');
                                            if (0 == obj.val()) {
                                                top_cate_col.show();
                                            }
                                            else {
                                                top_cate_col.hide();
                                            }
                                        }

                                        var select_parent_id = $('[name=parent_id]');
                                        select_parent_id.on('change', function () {
                                            check_cate_id(select_parent_id);
                                        });
                                        check_cate_id(select_parent_id);
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.other')@lang('common.info')@lang('common.template')</label>
                                <div class="col-sm-6">
                                    <ul id="extend" class="list-group"></ul>
                                </div>
                            </div>
                        </div>
                        <div id="extend_edit" class="col-sm-6">
                            <script type="text/javascript" src="{{ asset('js/M_exttpl_editor.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        @if ($edit_info['extend'])'def_data':{!! json_encode($edit_info['extend']) !!},
                                        @endif
                                        'run_type': 'add',
                                        'out_obj': $('#extend'),
                                        'edit_obj': $('#extend_edit'),
                                        'post_name': 'extend[]'
                                    };
                                    new M_exttpl_editor(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.attribute')@lang('common.template')</label>
                                <div id="attribute" class="col-sm-8">
                                    <script type="text/javascript"
                                            src="{{ asset('js/M_attribute_editor.js') }}"></script>
                                    <script type="text/javascript">
                                        $(function () {
                                            var config = {
                                                @if ($edit_info['attribute'])'def_data':{!! json_encode($edit_info['attribute']) !!},
                                                @endif
                                                'run_type': 'edit',
                                                'out_obj': $('#attribute'),
                                                'post_name': 'attribute'
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
                                <label class="col-sm-2 control-label">@lang('common.enable')@lang('common.category')@lang('common.content')</label>
                                <div class="col-sm-10">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_content" value="1"
                                               @if (1 === $edit_info['is_content'])checked="checked"@endif />
                                        @lang('common.enable')(@lang('common.content')@lang('common.template'))
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="is_content" value="0"
                                               @if (0 === $edit_info['is_content'] or '' === $edit_info['is_content'])checked="checked"@endif />
                                        @lang('common.disable')(@lang('common.list')@lang('common.template'))
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label class="col-sm-12">@lang('common.category')@lang('common.content')</label>
                        <textarea rows="15" class="col-sm-12" name="content">{{ $edit_info['content'] }}</textarea>
                    </div>
                    <div class="cb"></div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                @if (Route::is('*::add'))
                                    @lang('common.add')
                                @elseif (Route::is('*::edit'))
                                    @lang('common.edit')
                                @endif
                            </button>
                            <a href="{{ route('Admin::ArticleCategory::index') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @kindeditor(content)
@endsection