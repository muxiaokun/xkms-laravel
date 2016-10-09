<script type="text/javascript" src="{{ asset('js/M_cate_tree.js') }}"></script>
<script type="text/javascript">
    $(function () {
            @foreach ($article_category_list as $article_category)
        {//默认展开所有的子级分类,如果不展开提交时表单数据将不存在}
            @if ($article_category['checked'])
$('input[name="s_limit[{{ $article_category['id'] }}]"]').val('{{ $edit_info['ext_info'][$article_category['id']]['s_limit'] }}' || 0);
            $('select[name="template_list[{{ $article_category['id'] }}]"] option[value="{{ $edit_info['ext_info'][$article_category['id']]['template'] }}"]').prop('selected', true);
            $('select[name="list_template_list[{{ $article_category['id'] }}]"] option[value="{{ $edit_info['ext_info'][$article_category['id']]['list_template'] }}"]').prop('selected', true);
            $('select[name="article_template_list[{{ $article_category['id'] }}]"] option[value="{{ $edit_info['ext_info'][$article_category['id']]['article_template'] }}"]').prop('selected', true);
            M_cate_tree('input[name="category_list[]"][value="{{ $article_category['id'] }}"]', article_channel_cb);
            @endif
            @endforeach
        }
        );
</script>
<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" action="" method="post">
                <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.name')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control"
                                       placeholder="@lang('common.channel')@lang('common.name')" name="name"
                                       value="{{ $edit_info['name'] }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.keywords')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control"
                                       placeholder="@lang('common.channel')@lang('common.keywords')" name="keywords"
                                       value="{{ $edit_info['keywords'] }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.description')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control"
                                       placeholder="@lang('common.channel')@lang('common.description')"
                                       name="description" value="{{ $edit_info['description'] }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.other')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control"
                                       placeholder="@lang('common.channel')@lang('common.other')" name="other"
                                       value="{{ $edit_info['other'] }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.template')</label>
                            <div class="col-sm-6">
                                <select name="template" class="form-control input-sm w200 fl">
                                    <option value="">@lang('common.use')@lang('common.default')</option>
                                    @foreach ($channel_template_list as $template)
                                        <option value="{{ $template['value'] }}"
                                                @if ($template['value'] eq $edit_info['template'])selected="selected"@endif >{{ $template['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.yes')@lang('common.no')@lang('common.show')</label>
                            <div class="col-sm-3">
                                <label class="radio-inline">
                                    <input type="radio" name="if_show" value="1"
                                           @if ('1' heq $edit_info['if_show'] or !isset($edit_info['if_show']))checked="checked"@endif />@lang('common.show')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="if_show" value="0"
                                           @if ('0' heq $edit_info['if_show'])checked="checked"@endif />@lang('common.hidden')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                {//是否可以管理权限}
                @if ($manage_privilege)
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.admin')</label>
                                <div class="col-sm-6"><h4 id="manage_id_list" style="margin:2px 0px 0px 0px;"></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="admin_user_list">
                            <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        @if ($edit_info['manage_id'])'def_data':{{ $edit_info['manage_id'] }}, @endif
                                        'out_obj': $('#manage_id_list'),
                                        'edit_obj': $('#admin_user_list'),
                                        'post_name': 'manage_id[]',
                                        'ajax_url': '{{ route('ajax_api') }}',
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
                                <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.management')@lang('common.group')</label>
                                <div class="col-sm-6"><h4 id="manage_group_id_list"
                                                          style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="admin_group_list">
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        @if ($edit_info['manage_group_id'])'def_data':{{ $edit_info['manage_group_id'] }},
                                        @endif
                                        'out_obj': $('#manage_group_id_list'),
                                        'edit_obj': $('#admin_group_list'),
                                        'post_name': 'manage_group_id[]',
                                        'ajax_url': '{{ route('ajax_api') }}',
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
                            <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        @if ($edit_info['access_group_id'])'def_data':{{ $edit_info['access_group_id'] }},
                                        @endif
                                        'out_obj': $('#access_group_id_list'),
                                        'edit_obj': $('#access_group_list'),
                                        'post_name': 'access_group_id[]',
                                        'ajax_url': '{{ route('ajax_api') }}',
                                        'field': 'access_group_id'
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
                            <label class="col-sm-4 control-label">@lang('common.channel')@lang('common.category')</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-condensed table-hover">
                            <tr>
                                <th></th>
                                <th class="col-sm-1">@lang('common.list')@lang('common.number')</th>
                                <th class="col-sm-1">@lang('common.category')@lang('common.template')</th>
                                <th class="col-sm-1">@lang('common.list')@lang('common.template')</th>
                                <th class="col-sm-1">@lang('common.article')@lang('common.template')</th>
                            </tr>
                            @foreach ($article_category_list as $cate_key => $article_category)
                                <tr cate_id="{{ $article_category['id'] }}"
                                    parent_id="{{ $article_category['parent_id'] }}"
                                    has_child="{{ $article_category['has_child'] }}">
                                    <td>
                                        <span class="glyphicon @if (0 lt $article_category['has_child'])glyphicon-plus@elseglyphicon-minus@endif mlr10"
                                              onclick="M_cate_tree(this,article_channel_cb);"></span>
                                        <input type="checkbox" name="category_list[]"
                                               value="{{ $article_category['id'] }}"
                                               @if ($article_category['checked'])checked="checked"
                                               @endif onClick="M_cate_checkbox(this)"/>
                                        {{ $article_category['name'] }}(ID:{{ $article_category['id'] }})
                                    </td>
                                    <td @if (0 eq $cate_key)id="s_limit"@endif >
                                        <input type="text" name="s_limit[{{ $article_category['id'] }}]"
                                               style="width:100%;" onKeyup="M_in_int(this);"/>
                                    </td>
                                    <td @if (0 eq $cate_key)id="template_list"@endif >
                                        <select name="template_list[{{ $article_category['id'] }}]">
                                            <option value="">@lang('common.use')@lang('common.default')</option>
                                            @foreach ($template_list as $template)
                                                <option value="{{ $template['value'] }}">{{ $template['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td @if (0 eq $cate_key)id="list_template_list"@endif >
                                        <select name="list_template_list[{{ $article_category['id'] }}]">
                                            <option value="">@lang('common.use')@lang('common.default')</option>
                                            @foreach ($list_template_list as $template)
                                                <option value="{{ $template['value'] }}">{{ $template['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td @if (0 eq $cate_key)id="article_template_list"@endif >
                                        <select name="article_template_list[{{ $article_category['id'] }}]">
                                            <option value="">@lang('common.use')@lang('common.default')</option>
                                            @foreach ($article_template_list as $template)
                                                <option value="{{ $template['value'] }}">{{ $template['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="form-group col-sm-12 text-center">
                    <button type="submit" class="btn btn-info">
                        @if ($Think.const.ACTION_NAME eq 'add')
                            @lang('common.add')
                        @elseif ($Think.const.ACTION_NAME eq 'edit')
                            @lang('common.edit')
                        @endif
                    </button>
                    <a href="{{ route('index') }}" class="btn btn-default">
                        @lang('common.goback')
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
