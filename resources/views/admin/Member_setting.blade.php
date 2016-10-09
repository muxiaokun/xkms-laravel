<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            <form method="post" class="form-horizontal" role="form">
                <div class="row">
                    {{--会员是否启用--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.enable')@lang('common.member')@lang('common.controller')</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_MEMBER_ENABLE" value="1"
                                           @if ('1' === C('SYS_MEMBER_ENABLE'))checked="checked"@endif >@lang('common.open')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_MEMBER_ENABLE" value="0"
                                           @if ('0' === C('SYS_MEMBER_ENABLE'))checked="checked"@endif >@lang('common.close')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{--注册会员时是否立即启用--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.auto')@lang('common.enable')@lang('common.member')</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_MEMBER_AUTO_ENABLE" value="1"
                                           @if ('1' === C('SYS_MEMBER_AUTO_ENABLE'))checked="checked"@endif >@lang('common.open')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_MEMBER_AUTO_ENABLE" value="0"
                                           @if ('0' === C('SYS_MEMBER_AUTO_ENABLE'))checked="checked"@endif >@lang('common.close')
                                </label>
                            </div>
                        </div>
                    </div>
                    {{--是否启用前台验证码--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.frontend')@lang('common.verify_code')</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_FRONTEND_VERIFY" value="1"
                                           @if ('1' === C('SYS_FRONTEND_VERIFY'))checked="checked"@endif >@lang('common.open')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_FRONTEND_VERIFY" value="0"
                                           @if ('0' === C('SYS_FRONTEND_VERIFY'))checked="checked"@endif >@lang('common.close')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{--前台最大登录数--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.frontend')@lang('common.max')@lang('common.login')@lang('common.number')</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_FRONTEND_LOGIN_NUM"
                                       value="{{ config('system.sys_frontend_login_num') }}" class="form-control"
                                       onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                    {{--前台登录最大锁定时间--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.frontend')@lang('common.max')@lang('common.lock')@lang('common.time')
                                (@lang('common.second'))</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_FRONTEND_LOCK_TIME"
                                       value="{{ config('system.sys_frontend_lock_time') }}" class="form-control"
                                       onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{--前台自动登出时间--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.frontend')@lang('common.login')@lang('common.timeout')
                                (@lang('common.second'))</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_FRONTEND_TIMEOUT"
                                       value="{{ config('system.sys_frontend_timeout') }}" class="form-control"
                                       onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
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