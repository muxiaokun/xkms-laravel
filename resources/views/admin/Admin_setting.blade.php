@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form method="post" class="form-horizontal" role="form">
                    {{ csrf_field() }}
                    <div class="row">
                        {{--是否自动记录管理员日志--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.auto')@lang('common.record')@lang('common.log')</label>
                                <div class="col-sm-7">
                                    <label class="radio-inline">
                                        <input type="radio" name="sys_admin_auto_log" value="1"
                                               @if ('1' === config('system.sys_admin_auto_log'))checked="checked"@endif >@lang('common.open')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="sys_admin_auto_log" value="0"
                                               @if ('0' === config('system.sys_admin_auto_log'))checked="checked"@endif >@lang('common.close')
                                    </label>
                                </div>
                            </div>
                        </div>
                        {{--是否启用后台验证码--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.backend')@lang('common.verify_code')</label>
                                <div class="col-sm-7">
                                    <label class="radio-inline">
                                        <input type="radio" name="sys_backend_verify" value="1"
                                               @if ('1' === config('system.sys_backend_verify'))checked="checked"@endif >@lang('common.open')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="sys_backend_verify" value="0"
                                               @if ('0' === config('system.sys_backend_verify'))checked="checked"@endif >@lang('common.close')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{--后台最大登录数--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.backend')@lang('common.max')@lang('common.login')@lang('common.number')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="sys_backend_login_num"
                                           value="{{ config('system.sys_backend_login_num') }}" class="form-control"
                                           onKeyup="M_in_int(this);">
                                </div>
                            </div>
                        </div>
                        {{--后台登录最大锁定时间--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.backend')@lang('common.max')@lang('common.lock')@lang('common.time')
                                    (@lang('common.second'))</label>
                                <div class="col-sm-7">
                                    <input type="text" name="sys_backend_lock_time"
                                           value="{{ config('system.sys_backend_lock_time') }}" class="form-control"
                                           onKeyup="M_in_int(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{--后台自动登出时间--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.backend')@lang('common.login')@lang('common.timeout')
                                    (@lang('common.second'))</label>
                                <div class="col-sm-7">
                                    <input type="text" name="sys_backend_timeout"
                                           value="{{ config('system.sys_backend_timeout') }}" class="form-control"
                                           onKeyup="M_in_int(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">@lang('common.save')</button>
                            <input class="btn btn-default" type="reset" value="@lang('common.reset')">
                            <a href="{{ route('Admin::Index::main') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection