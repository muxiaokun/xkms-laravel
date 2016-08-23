    <section class="container Member">
        <div class="row">
            <div class="col-sm-6">
                <img class="col-sm-12 login-logo" src="__PUBLIC__/css/bimages/app_logo.png" />
            </div>
            <div class="col-sm-6">
                <div class="tab-content">
                    <div class="tab-pane @if (register neq I('t')">active@endif mt20 h350" id="login)
                        <form class="form-horizontal" role="form" method="post"">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{ trans('common.account') }}</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.account') }}" name="user" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{ trans('common.pwd') }}</label>
                                <div class="col-sm-5">
                                    <input type="password" class="form-control" placeholder="{{ trans('common.pwd') }}" name="pwd" />
                                </div>
                            </div>
                            @if (C('SYS_FRONTEND_VERIFY'))
                            {/*<!--验证码 开始-->*/}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{ trans('common.verify_code') }}</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.verify_code') }}" name="verify" style="text-transform:uppercase;" />
                                </div>
                            </div>
                            <div class="form-group">
                                <img class="col-sm-5 col-sm-offset-3" src="{:M_U('verify_img',array('t'=>'login'))}" onClick="M_change_verify(this,$('input[name=verify]'))" />
                            </div>
                            {/*<!--验证码 结束-->*/}
                            @endif
                            <input type="submit" class="btn btn-info col-sm-offset-5" value="{{ trans('common.login') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>