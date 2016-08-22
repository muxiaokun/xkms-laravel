    <script type="text/javascript" charset="utf-8">
    if(window.top !== window.self){ window.top.location=window.location;}
    </script>
    <section class="container">
        <div class="col-sm-6 col-sm-offset-3 login">
            <div class="panel panel-primary">
                <div class="panel-heading">{$Think.const.APP_NAME} {{ trans('common.dash') }} {{ trans('common.login') }}{{ trans('common.backend') }}</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="post" action="{:U('login')}">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.account') }}</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" placeholder="{{ trans('common.account') }}" name="user" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.pwd') }}</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" placeholder="{{ trans('common.pwd') }}" name="pwd" />
                            </div>
                        </div>
                        <if condition="C('SYS_BACKEND_VERIFY')">
                        {/*<!--验证码 开始-->*/}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.verify_code') }}</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" placeholder="{{ trans('common.verify_code') }}" name="verify" style="text-transform:uppercase;" />
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <M:Img class="col-sm-offset-4 col-sm-4" style="max-height:50px;" src="{:U('verify_img')}" onClick="M_change_verify(this,$('input[name=verify]'))" />
                        </div>
                        {/*<!--验证码 结束-->*/}
                        </if>
                        <input type="submit" class="btn btn-primary col-sm-offset-5" value="{{ trans('common.login') }}">
                    </form>
                </div>
            </div>
        </div>
        <abbr class="text-center col-sm-6 col-sm-offset-3">
            {{ trans('common.note_browser') }}
        </abbr>
    </section>