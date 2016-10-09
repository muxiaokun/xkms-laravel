<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            <form method="post" class="form-horizontal" role="form">
                <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                @lang('common.messageboard')@lang('common.name')
                            </label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="name" value="{{ $edit_info['name'] }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.messageboard')@lang('common.template')</label>
                            <div class="col-sm-6">
                                <select name="template" class="form-control input-sm">
                                    <option value="">@lang('common.use')@lang('common.default')</option>
                                    @foreach ($template_list as $template)
                                        <option value="{{ $template['value'] }}"
                                                @if ($template['value'] eq $edit_info['template'])selected="selected"@endif >{{ $template['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-hover">
                    <tr>
                        <th class="col-sm-6 text-center">@lang('common.submit')@lang('common.option')</th>
                        <th class="col-sm-2 text-center">@lang('common.submit')@lang('common.type')</th>
                        <th class="col-sm-4 text-center">@lang('common.option')@lang('common.condition')</th>
                        <th id="btn_obj" class="col-sm-2 text-center"></th>
                    </tr>
                    <tr id="edit_obj" style="display: none">
                        <td class="col-sm-6">
                            <input class="form-control" mtype="msg_name"/>
                        </td>
                        <td class="col-sm-2">
                            <select class="form-control" mtype="msg_type">
                                <option value="text">@lang('common.text')</option>
                                <option value="radio">@lang('common.radio')(name:var1,var2)</option>
                                <option value="checkbox">@lang('common.checkbox')(name:var1,var2)</option>
                                <option value="textarea">@lang('common.textarea')</option>
                            </select>
                        </td>
                        <td class="col-sm-4" colspan="2">
                            <div class="form-group">
                                <label class="col-sm-3 control-label checkbox checkbox-inline">
                                    <input type="checkbox" value="required"
                                           mtype="msg_required"/>@lang('common.required')
                                </label>
                                <label class="col-sm-3 control-label" style="font-weight:normal;">
                                    @lang('common.max')@lang('common.length')
                                </label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="text" value="" onKeyup="M_in_int(this);"
                                           mtype="msg_length"/>
                                </div>
                                <a class="btn btn-danger" href="javascript:void(0);">@lang('common.del')</a>
                            </div>
                        </td>
                    </tr>
                </table>
                <table id="out_obj" class="table table-hover">
                    <script type="text/javascript" src="{{ asset('js/M_messageboard_editor.js') }}"></script>
                    <script>
                        $(function () {
                            new M_messageboard_editor({
                                @if ($edit_info['config'])'def_data':{{ $edit_info['config'] }}, @endif
                                'out_obj': $('#out_obj'),
                                'edit_obj': $('#edit_obj'),
                                'btn_obj': $('#btn_obj'),
                                'post_name': 'config'
                            });
                        });
                    </script>
                </table>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">@lang('common.save')</button>
                        <input class="btn btn-default" type="reset" value="@lang('common.reset')">
                        <a href="{{ route('main') }}" class="btn btn-default">
                            @lang('common.goback')
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>