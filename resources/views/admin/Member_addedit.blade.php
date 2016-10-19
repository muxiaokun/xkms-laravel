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
                                'member_name': Array('member_name', 'id'),
                                'password': Array('password', 'is_pwd'),
                                'password_again': Array('password', 'password_again', 'is_pwd'),
                                'email': Array('email'),
                                'phone': Array('phone')
                            },
                            'ajax_url': "{{ route('ajax_api') }}",
                        };
                        new M_valid(config);
                    });
                </script>
                <form id="form_valid" onSubmit="return false;" class="form-horizontal" role="form" action=""
                      method="post">
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <input type="hidden" name="is_pwd" value="@if ($Think.const.ACTION_NAME == 'add')1@else0@endif"/>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.member')@lang('common.name')</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.member')@lang('common.name')" name="member_name"
                                           value="{{ $edit_info['member_name'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.member')@lang('common.pass')</label>
                                <div class="col-sm-3">
                                    <input type="password" class="form-control"
                                           placeholder="@lang('common.member')@lang('common.pass')" name="password"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.again')@lang('common.input')@lang('common.pass')</label>
                                <div class="col-sm-3">
                                    <input type="password" class="form-control"
                                           placeholder="@lang('common.again')@lang('common.input')@lang('common.pass')"
                                           name="password_again"/>
                                    @if ($edit_info)
                                        <span class="help-block">@lang('common.not_input_pass')</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.member')@lang('common.phone')</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.member')@lang('common.phone')" name="phone"
                                           value="{{ $edit_info['phone'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">@lang('common.member')@lang('common.email')</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control"
                                           placeholder="@lang('common.member')@lang('common.email')" name="email"
                                           value="{{ $edit_info['email'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.member')@lang('common.yes')@lang('common.no')@lang('common.enable')</label>
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
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.pertain')@lang('common.group')</label>
                                <div class="col-sm-6"><h4 id="group_id_list" style="margin:2px 0px 0px 0px;"></h4></div>
                            </div>
                        </div>
                        <div class="col-sm-6" id="group_list">
                            <script type="text/javascript" src="{{ asset('js/M_select_add.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        @if ($edit_info['group_id'])'def_data':{{ $edit_info['group_id'] }}, @endif
                                        'out_obj': $('#group_id_list'),
                                        'edit_obj': $('#group_list'),
                                        'post_name': 'group_id[]',
                                        'ajax_url': '{{ route('ajax_api') }}',
                                        'field': 'group_id'
                                    };
                                    new M_select_add(config);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row mt10">
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
                </form>
            </div>
        </div>
    </section>
@endsection