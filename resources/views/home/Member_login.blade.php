    <script type="text/javascript" charset="utf-8">
    if(window.top !== window.self){ window.top.location=window.location;}
    $(function () {
      $('#login_tab').tab()
    })
    </script>
    <section class="container Member">
        <div class="row">
            <div class="col-sm-6">
                <img class="col-sm-12 login-logo" src="__PUBLIC__/css/bimages/app_logo.png" />
            </div>
            <div class="col-sm-6">
                <ul id="login_tab" class="nav nav-tabs" role="tablist">
                    <li <if condition="register neq I('t')">class="active"</if> ><a href="#login" role="tab" data-toggle="tab">{{ trans('common.login') }}{{ trans('common.account') }}</a></li>
                    <li <if condition="register eq I('t')">class="active"</if> ><a href="#register" role="tab" data-toggle="tab">{{ trans('common.register') }}{{ trans('common.account') }}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane <if condition="register neq I('t')">active</if> mt20 h350" id="login">
                        <import file="js/M_valid" />
                        <script>
                            $(function(){
                                var config = {
                                    'form_obj':$('#form_valid'),
                                    'check_list':{
                                        'user':Array('user'),
                                    },
                                    'ajax_url':"{:M_U('ajax_api')}",
                                };
                                new M_valid(config);
                            });
                        </script>
                        <form id="form_valid_login" class="form-horizontal" role="form" method="post" action="{:M_U('login')}">
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
                            <if condition="C('SYS_FRONTEND_VERIFY')">
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
                            </if>
                            <input type="submit" class="btn btn-info col-sm-offset-5" value="{{ trans('common.login') }}">
                        </form>
                    </div>
                    <div class="tab-pane <if condition="register eq I('t')">active</if> mt20 h350" id="register">
                        <script>
                            $(function(){
                                var config = {
                                    'form_obj':$('#form_valid'),
                                    'check_list':{
                                        're_member_name':Array('re_member_name'),
                                        'password':Array('password'),
                                        'password_again':Array('password','password_again')
                                    },
                                    'ajax_url':"{:M_U('ajax_api')}",
                                };
                                new M_valid(config);
                            });
                        </script>
                        <form id="form_valid_register" class="form-horizontal" role="form" method="post" action="{:M_U('register')}">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{ trans('common.account') }}</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.account') }}" name="re_member_name" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{ trans('common.input') }}{{ trans('common.pwd') }}</label>
                                <div class="col-sm-5">
                                    <input type="password" class="form-control" placeholder="{{ trans('common.pwd') }}" name="password" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{ trans('common.again') }}{{ trans('common.input') }}</label>
                                <div class="col-sm-5">
                                    <input type="password" class="form-control" placeholder="{{ trans('common.pwd') }}" name="password_again" />
                                </div>
                            </div>
                            <if condition="C('SYS_FRONTEND_VERIFY')">
                            {/*<!--验证码 开始-->*/}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{ trans('common.verify_code') }}</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" placeholder="{{ trans('common.verify_code') }}" name="verify" style="text-transform:uppercase;" />
                                </div>
                            </div>
                            <div class="form-group">
                                <img class="col-sm-5 col-sm-offset-3" src="{:M_U('verify_img',array('t'=>'register'))}" onClick="M_change_verify(this,$('input[name=verify]'))" />
                            </div>
                            {/*<!--验证码 结束-->*/}
                            </if>
                            <input type="submit" class="btn btn-info col-sm-offset-5" value="{{ trans('common.register') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>