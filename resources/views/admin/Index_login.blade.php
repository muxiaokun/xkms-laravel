<script type="text/javascript" charset="utf-8">
    if (window.top !== window.self) {
        window.top.location = window.location;
    }
</script>
<section class="container">
    <div class="col-sm-6 col-sm-offset-3 login">
        <div class="panel panel-primary">
            <div class="panel-heading">@lang('common.app_name')@lang('common.dash') @lang('common.login')@lang('common.backend')</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="post" action="{{ route('Admin::Index::login') }}">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('common.account')</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="@lang('common.account')" name="user"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('common.pwd')</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" placeholder="@lang('common.pwd')" name="pwd"/>
                        </div>
                    </div>
                    @if (config('system.sys_backend_verify'))
                        {{--验证码 开始--}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.verify_code')</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" placeholder="@lang('common.verify_code')"
                                       name="verify" style="text-transform:uppercase;"/>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <M:Img class="col-sm-offset-4 col-sm-4" style="max-height:50px;"
                                   src="{{ route('verify_img') }}"
                                   onClick="M_change_verify(this,$('input[name=verify]'))"/>
                        </div>
                        {{--验证码 结束--}}
                    @endif
                    <input type="submit" class="btn btn-primary col-sm-offset-5" value="@lang('common.login')">
                </form>
            </div>
        </div>
    </div>
    <abbr class="text-center col-sm-6 col-sm-offset-3">
        @lang('common.note_browser')
    </abbr>
</section>