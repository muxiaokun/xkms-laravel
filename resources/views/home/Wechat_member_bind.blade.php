@extends('home.layout')
@section('body')
    <section class="container Member">
        <div class="row">
            <div class="col-sm-6">
                <img class="col-sm-12 login-logo" src="{{ asset('/css/bimages/app_logo.png') }}"/>
            </div>
            <div class="col-sm-6">
                <div class="tab-content">
                    <div class="tab-pane @if ('register' != request('t')) active @endif mt20 h350" id="login">
                        <form class="form-horizontal" role="form" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.account')</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" placeholder="@lang('common.account')"
                                           name="user"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.pwd')</label>
                                <div class="col-sm-5">
                                    <input type="password" class="form-control" placeholder="@lang('common.pwd')"
                                           name="pwd"/>
                                </div>
                            </div>
                            @if (config('system.sys_frontend_verify'))
                                {{--验证码 开始--}}
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">@lang('common.verify_code')</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control"
                                               placeholder="@lang('common.verify_code')"
                                               name="verify" style="text-transform:uppercase;"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    @asyncImg(<img class="col-sm-4 col-sm-offset-2"
                                         src="{{ route('VerificationCode',['name'=>'login']) }}"
                                         onClick="M_change_verify(this,$('input[name=verify]'))"/>)
                                </div>
                                {{--验证码 结束--}}
                            @endif
                            <input type="submit" class="btn btn-info col-sm-offset-5" value="@lang('common.login')">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection