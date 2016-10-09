<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            <form method="post" class="form-horizontal" role="form">
                <div class="row">
                    {{--是否自动记录管理员日志--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.auto')@lang('common.record')@lang('common.log')</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_ADMIN_AUTO_LOG" value="1"
                                           @if ('1' === C('SYS_ADMIN_AUTO_LOG'))checked="checked"@endif >@lang('common.open')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_ADMIN_AUTO_LOG" value="0"
                                           @if ('0' === C('SYS_ADMIN_AUTO_LOG'))checked="checked"@endif >@lang('common.close')
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
                                    <input type="radio" name="SYS_BACKEND_VERIFY" value="1"
                                           @if ('1' === C('SYS_BACKEND_VERIFY'))checked="checked"@endif >@lang('common.open')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_BACKEND_VERIFY" value="0"
                                           @if ('0' === C('SYS_BACKEND_VERIFY'))checked="checked"@endif >@lang('common.close')
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
                                <input type="text" name="SYS_BACKEND_LOGIN_NUM"
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
                                <input type="text" name="SYS_BACKEND_LOCK_TIME"
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
                                <input type="text" name="SYS_BACKEND_TIMEOUT"
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
                        <a href="{{ route('main') }}" class="btn btn-default">
                            @lang('common.goback')
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>