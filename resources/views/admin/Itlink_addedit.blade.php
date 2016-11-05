@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <script type="text/javascript" src="{{ asset('js/M_valid.js') }}"></script>
                <script>
                    $(function () {
                        var config = {
                            'form_obj': $('#form_valid'),
                            'check_list': {
                                'short_name': Array('short_name', 'id')
                            },
                            'ajax_url': "{{ route('Admin::Itlink::ajax_api') }}",
                        };
                        new M_valid(config);
                    });
                </script>
                <form id="form_valid" onSubmit="return false;" class="form-horizontal" role="form" action=""
                      method="post">
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('itlink.itlink')@lang('common.name')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('itlink.itlink')@lang('common.name')" name="name"
                                           value="{{ $edit_info['name'] }}"
                                           onchange="M_zh2py(this,'input[name=short_name]')"
                                           link="{{ route('Admin::Itlink::ajax_api') }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.short')@lang('common.name')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.short')@lang('common.name')" name="short_name"
                                           value="{{ $edit_info['short_name'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.yes')@lang('common.no')@lang('common.enable')</label>
                                <div class="col-sm-4">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_enable" value="1"
                                               @if ('1' === $edit_info['is_enable'] or !isset($edit_info['is_enable']))checked="checked"@endif />@lang('common.yes')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="is_enable" value="0"
                                               @if ('0' === $edit_info['is_enable'])checked="checked"@endif />@lang('common.no')
                                    </label>
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
                        <M:Timepicker start="start_time" end="end_time"/>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.max')@lang('common.show')@lang('common.number')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.max')@lang('common.show')@lang('common.number')"
                                           name="max_show_num" value="{{ $edit_info['max_show_num'] }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.show')@lang('common.number')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.show')@lang('common.number')" name="show_num"
                                           value="{{ $edit_info['show_num'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.max')@lang('common.click')@lang('common.number')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.max')@lang('common.click')@lang('common.number')"
                                           name="max_hit_num" value="{{ $edit_info['max_hit_num'] }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.click')@lang('common.number')</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.click')@lang('common.number')" name="hit_num"
                                           value="{{ $edit_info['hit_num'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.add')@lang('itlink.itlink')</label>
                                <div id="upload_btns" class="col-sm-10">
                                    <button id="uploadsbutton" type="button" class="btn btn-default">
                                        @lang('common.upload')@lang('common.image')@lang('common.group')
                                    </button>
                                    <M:Uploadfile id="uploadsbutton" type="multiimage" dir="kindeditor"
                                                  cb_fn="M_callback_itlink"/>
                                </div>
                            </div>
                            <script type="text/javascript" src="{{ asset('js/M_itlink_editor.js') }}"></script>
                            <script type="text/javascript">
                                //向全局注册 变量 空回调函数 用于kind和itlink_editor沟通
                                var M_callback_itlink = function () {
                                    console.log("bind M_itlink_editor error");
                                }
                                $(function () {
                                    var config = {
                                        @if ($edit_info['ext_info'])'def_data':{{ $edit_info['ext_info']|json_encode }},
                                        @endif
                                        'global_var': 'itlink_editor',
                                        'out_obj': $('#ext_info'),
                                        'upload_btn': '#uploadsbutton',
                                        'callback_fn': 'M_callback_itlink',
                                        'def_image': '{:M_exists()}',
                                        'post_name': 'ext_info'
                                    };
                                    window.itlink_editor = new M_itlink_editor(config);
                                });
                            </script>
                            <!--ext_info[name][] ext_info[link][] ext_info[link_type][] ext_info[link_target][]-->
                        </div>
                        <div id="ext_info" class="col-sm-12">
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                @if (Route::is('*::add'))
                                    @lang('common.add')
                                @elseif (Route::is('*::edit'))
                                    @lang('common.edit')
                                @endif
                            </button>
                            <a href="{{ route('Admin::Itlink::index') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection