<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" action="" method="post">
                <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.assess')@lang('common.title')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('common.title')" name="title"
                                       value="{{ $edit_info['title'] }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.assess')@lang('common.group')</label>
                            <div class="col-sm-8" id="group_level">
                                <input type="hidden" name="group_level"/>
                                <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                                <script type="text/javascript">
                                    $(function () {
                                        var config = {
                                            @if ($edit_info['group_level'])'def_data': {
                                                'value': '{{ $edit_info['group_level'] }}',
                                                'html': '{{ $edit_info['group_name'] }}'
                                            }, @endif
                                            'edit_obj': $('#group_level'),
                                            'post_name': 'group_level',
                                            'ajax_url': '{{ route('ajax_api') }}',
                                            'field': 'group_level'
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
                            <label class="col-sm-4 control-label">@lang('common.assess')@lang('common.start')@lang('common.time')</label>
                            <div class="col-sm-6 ">
                                <input type="text" class="form-control"
                                       placeholder="@lang('common.start')@lang('common.time')" name="start_time"
                                       value="{{ $edit_info['start_time']|M_date=config('system.sys_date_detail') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.assess')@lang('common.end')@lang('common.time')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control"
                                       placeholder="@lang('common.end')@lang('common.time')" name="end_time"
                                       value="{{ $edit_info['end_time']|M_date=config('system.sys_date_detail') }}"/>
                            </div>
                        </div>
                    </div>
                    <M:Timepicker start="start_time" end="end_time"/>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.assess')@lang('common.yes')@lang('common.no')@lang('common.enable')</label>
                            <div class="col-sm-6">
                                <label class="radio-inline">
                                    <input type="radio" name="is_enable" value="1"
                                           @if ('1' === $edit_info['is_enable'] or !isset($edit_info['is_enable']))checked="checked"@endif />@lang('common.enable')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_enable" value="0"
                                           @if ('0' === $edit_info['is_enable'])checked="checked"@endif />@lang('common.disable')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.assess')@lang('common.target')</label>
                            <div class="col-sm-6">
                                <select class="form-control input-sm w260" name="target">
                                    <option value="member"
                                            @if ('member' == $edit_info['target'])selected="selected"@endif >@lang('common.member')</option>
                                    <option value="member_group"
                                            @if ('member_group' ==  $edit_info['target'])selected="selected"@endif >@lang('common.member')@lang('common.group')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">@lang('common.assess')@lang('common.explains')</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" style="width:100%;resize:none;"
                                          name="explains">{{ $edit_info['explains'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">
                            @if ($Think.const.ACTION_NAME == 'add')
                                @lang('common.add')
                            @elseif ($Think.const.ACTION_NAME == 'edit')
                                @lang('common.edit')
                            @endif
                        </button>
                        <a href="{{ route('index') }}" class="btn btn-default">
                            @lang('common.goback')
                        </a>
                    </div>
                </div>
                <div class="row" id="quests_edit">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.project')</label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" mtype="p"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.factor')</label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" mtype="f"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.max')@lang('common.minute')@lang('common.number')</label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" mtype="mg" onKeyup="M_in_int(this);"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.max')@lang('common.minute')@lang('common.number')</label>
                            <div class="col-sm-6">
                                <a class="btn btn-default" href="javascript:void(0);"
                                   mtype="add_assess">@lang('common.add')@lang('common.project')</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>@lang('common.project')</th>
                                <th>@lang('common.factor')</th>
                                <th>@lang('common.max')@lang('common.grade')</th>
                                <th class="col-sm-2"></th>
                            </tr>
                            </thead>
                            <tbody id="assess_area">

                            </tbody>
                        </table>
                    </div>
                </div>
                <script type="text/javascript" src="{{ asset('js/M_assess_editor.js') }}"></script>
                <script type="text/javascript">
                    var config = {
                        @if ($edit_info['ext_info'])'def_data':{{ $edit_info['ext_info'] }}, @endif
                        'out_obj': $('#assess_area'),
                        'edit_obj': $('#quests_edit'),
                        'post_name': 'ext_info'
                    }
                    new M_assess_editor(config);
                </script>
            </form>
        </div>
    </div>
</section>