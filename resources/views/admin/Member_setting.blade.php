
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <div class="row">
                    {/*<!--会员是否启用-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.enable}{$Think.lang.member}{$Think.lang.controller}</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_MEMBER_ENABLE" value="1" <if condition="'1' heq C('SYS_MEMBER_ENABLE')">checked="checked"</if> >{$Think.lang.open}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_MEMBER_ENABLE" value="0" <if condition="'0' heq C('SYS_MEMBER_ENABLE')">checked="checked"</if> >{$Think.lang.close}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--注册会员时是否立即启用-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.auto}{$Think.lang.enable}{$Think.lang.member}</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_MEMBER_AUTO_ENABLE" value="1" <if condition="'1' heq C('SYS_MEMBER_AUTO_ENABLE')">checked="checked"</if> >{$Think.lang.open}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_MEMBER_AUTO_ENABLE" value="0" <if condition="'0' heq C('SYS_MEMBER_AUTO_ENABLE')">checked="checked"</if> >{$Think.lang.close}
                                </label>
                            </div>
                        </div>
                    </div>
                    {/*<!--是否启用前台验证码-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.frontend}{$Think.lang.verify_code}</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_FRONTEND_VERIFY" value="1" <if condition="'1' heq C('SYS_FRONTEND_VERIFY')">checked="checked"</if> >{$Think.lang.open}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_FRONTEND_VERIFY" value="0" <if condition="'0' heq C('SYS_FRONTEND_VERIFY')">checked="checked"</if> >{$Think.lang.close}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--前台最大登录数-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.frontend}{$Think.lang.max}{$Think.lang.login}{$Think.lang.number}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_FRONTEND_LOGIN_NUM" value="{:C('SYS_FRONTEND_LOGIN_NUM')}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                    {/*<!--前台登录最大锁定时间-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.frontend}{$Think.lang.max}{$Think.lang.lock}{$Think.lang.time}({$Think.lang.second})</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_FRONTEND_LOCK_TIME" value="{:C('SYS_FRONTEND_LOCK_TIME')}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--前台自动登出时间-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{$Think.lang.frontend}{$Think.lang.login}{$Think.lang.timeout}({$Think.lang.second})</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_FRONTEND_TIMEOUT" value="{:C('SYS_FRONTEND_TIMEOUT')}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">{$Think.lang.save}</button>
                        <input class="btn btn-default" type="reset" value="{$Think.lang.reset}">
                        <a href="{:U('main')}" class="btn btn-default">
                            {$Think.lang.goback}
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>