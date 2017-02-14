@extends('admin.layout')
@section('body')
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
                                <label class="col-sm-4 control-label">@lang('common.recruit')@lang('common.name')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.recruit')@lang('common.name')" name="title"
                                           value="{{ $edit_info['title'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.current')@lang('common.recruit')@lang('common.number')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="current_portion"
                                           value="{{ $edit_info['current_portion'] }}" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.max')@lang('common.recruit')@lang('common.number')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="max_portion"
                                           value="{{ $edit_info['max_portion'] }}" onKeyup="M_in_int(this);"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.start')@lang('common.time')</label>
                                <div class="col-sm-6 ">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.start')@lang('common.time')" name="start_time"
                                           value="{{ mDate($edit_info['start_time']) }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.end')@lang('common.time')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.end')@lang('common.time')" name="end_time"
                                           value="{{ mDate($edit_info['end_time']) }}"/>
                                </div>
                            </div>
                        </div>
                        @timepicker(start_time,end_time)
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.yes')@lang('common.no')@lang('common.enable')</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_enable" value="1"
                                               @if (1 === $edit_info['is_enable'] or '' === $edit_info['is_enable'])checked="checked"@endif />@lang('common.enable')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="is_enable" value="0"
                                               @if (0 === $edit_info['is_enable'])checked="checked"@endif />@lang('common.disable')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div id="edit_obj" class="form-group">
                                <div class="col-sm-4 text-right">
                                    <button type="button" class="btn btn-default"
                                            mtype="in_add">@lang('common.add')@lang('common.extend')@lang('common.info')</button>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" mtype="in_val"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="ext_info_list" class="row">
                        <script type="text/javascript" src="{{ asset('js/M_exttpl_editor.js') }}"></script>
                        <script type="text/javascript">
                            $(function () {
                                var config = {
                                    @if ($edit_info['ext_info'])'def_data':{{ $edit_info['ext_info']|json_encode }},
                                    @endif
                                    'run_type': 'add_edit',
                                    'out_obj': $('#ext_info_list'),
                                    'edit_obj': $('#edit_obj'),
                                    'post_name': 'ext_info'
                                };
                                new M_exttpl_editor(config);
                            });
                        </script>
                    </div>
                    <div class="col-sm-12">
                        <label class="col-sm-12">@lang('common.recruit')@lang('common.content')</label>
                        <textarea rows="15" class="col-sm-12" name="explains">{{ $edit_info['explains'] }}</textarea>
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
                            <a href="{{ route('Admin::Recruit::index') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @kindeditor(explains)
@endsection