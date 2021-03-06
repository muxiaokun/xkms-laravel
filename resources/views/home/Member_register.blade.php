@extends('home.layout')
@section('body')
    <script type=" text/javascript" src="{{ asset('js/M_valid.js') }}"></script>
    <section class="container Member">
        <div class="row">
            <div class="col-sm-6">
                <img class="col-sm-12 login-logo" src="{{ asset('/css/bimages/app_logo.png') }}"/>
            </div>
            <div class="col-sm-6">
                <ul class="nav nav-tabs">
                    <li>
                        <a href="{{ route('Home::Member::index') }}">
                            @lang('common.login')@lang('common.account')
                        </a>
                    </li>
                    <li class="active">
                        <a href="javascript:void(0);">
                            @lang('common.register')@lang('common.account')
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active mt20 h350">
                        <script>
                            $(function () {
                                var config = {
                                    'form_obj': $('#form_valid_register'),
                                    'check_list': {
                                        're_member_name': Array('re_member_name'),
                                        'password': Array('password'),
                                        'password_again': Array('password', 'password_again')
                                    },
                                    'ajax_url': " {{ route('Home::Member::ajax_api') }}",
                                };
                                new M_valid(config);
                            });
                        </script>
                        <form id="form_valid_register" class="form-horizontal" role="form" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.account')</label>
                                <div class="col-sm-5"><input type="text" class="form-control" placeholder=
                                    "@lang('common.account')" name="re_member_name"/>
                                </div>
                            </div>
                            <div class=
                                 "form-group"><label class="col-sm-3 control-label"
                                >@lang('common.input')@lang('common.pwd')</label>
                                <div class="col-sm-5"><input
                                            type="password" class="form-control" placeholder="@lang('common.pwd')" name=
                                    "password"/></div>
                            </div>
                            <div class="form-group"><label
                                        class="col-sm-3 control-label">@lang('common.again')@lang('common.input')</label>
                                <div class="col-sm-5"><input type="password" class="form-control"
                                                             placeholder="@lang('common.pwd')" name="password_again"/>
                                </div>
                            </div>
                            @if (config('system.sys_frontend_verify'))
                                {{--验证码 开始--}}
                                <div class="form-group"><label
                                            class="col-sm-3 control-label">@lang('common.verify_code')</label>
                                    <div class="col-sm-5"><input type="text" class="form-control"
                                                                 placeholder="@lang('common.verify_code')" name="verify"
                                                                 style="text-transform:uppercase;"/></div>
                                </div>
                                <div class="form-group">
                                    @asyncImg(<img class="col-sm-5 col-sm-offset-3"
                                         src="{{ route('VerificationCode',['name'=>'register']) }}"
                                         onClick="M_change_verify(this,$('input[name=verify]'))"/>)
                                </div>
                                {{--验证码 结束--}}
                            @endif
                            <input type="submit" class="btn btn-info col-sm-offset-5" value="@lang('common.register')">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection