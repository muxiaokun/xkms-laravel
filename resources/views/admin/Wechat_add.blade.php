
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <div class="row">
                    {/*<!--Api_link-->*/}
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Api_link</label>
                            <div class="col-sm-7">
                                <a href="javascript:void(0);" id="Api_link" data-clipboard-text="{{ $Api_link }}" >
                                    {{ $Api_link }}
                                </a>
                                <br/>{{ trans('common.click') }}{{ trans('common.path') }}{{ trans('common.copy') }}
                                <a href="{:M_qrcode($Api_link)}" target="_blank" >
                                    <M:Img class="w100 h100" src="{:M_qrcode($Api_link)}" />
                                </a>
                                <script type="text/javascript" charset="utf-8">M_ZeroClipboard('Api_link');</script>
                            </div>
                        </div>
                    </div>
                    {/*<!--Oauth2_link-->*/}
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Oauth2_link</label>
                            <div class="col-sm-7">
                                <a href="javascript:void(0);" id="Oauth2_link" data-clipboard-text="{{ $Oauth2_link }}" >
                                    {{ $Oauth2_link }}
                                </a>
                                <br/>{{ trans('common.click') }}{{ trans('common.path') }}{{ trans('common.copy') }}
                                <a href="{:M_qrcode($Oauth2_link)}" target="_blank" >
                                    <M:Img class="w100 h100" src="{:M_qrcode($Oauth2_link)}" />
                                </a>
                                <script type="text/javascript" charset="utf-8">M_ZeroClipboard('Oauth2_link');</script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--WECHAT_ID-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">appId</label>
                            <div class="col-sm-7">
                                <input type="text" name="WECHAT_ID" value="{{ config('WECHAT_ID') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    {/*<!--WECHAT_SECRET-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">appSecret</label>
                            <div class="col-sm-7">
                                <input type="text" name="WECHAT_SECRET" value="{{ config('WECHAT_SECRET') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--WECHAT_TOKEN-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">token</label>
                            <div class="col-sm-7">
                                <input type="text" name="WECHAT_TOKEN" value="{{ config('WECHAT_TOKEN') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    {/*<!--是否自动记录Wechat日志-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.enable') }}{{ trans('common.record') }}{{ trans('common.log') }}</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="WECHAT_RECORD_LOG" value="1" <if condition="'1' heq $wechat['WECHAT_RECORD_LOG'] or !isset($wechat['WECHAT_RECORD_LOG'])">checked="checked"</if> >{{ trans('common.open') }}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="WECHAT_RECORD_LOG" value="0" <if condition="'0' heq $wechat['WECHAT_RECORD_LOG']">checked="checked"</if> >{{ trans('common.close') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--WECHAT_AESKEY-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">AesKey</label>
                            <div class="col-sm-7">
                                <input type="text" name="WECHAT_AESKEY" value="{{ config('WECHAT_AESKEY') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    {/*<!--WECHAT_TEMPLATE_ID-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">templateId</label>
                            <div class="col-sm-7">
                                <input type="text" name="WECHAT_TEMPLATE_ID" value="{{ config('WECHAT_TEMPLATE_ID') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">{{ trans('common.save') }}</button>
                        <input class="btn btn-default" type="reset" value="{{ trans('common.reset') }}">
                        <a href="{{ route('index') }}" class="btn btn-default">
                            {{ trans('common.goback') }}
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>