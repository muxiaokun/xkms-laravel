
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <script type="text/javascript" src="{{ asset('js/M_valid.js') }}"></script>
                <script>
                    $(function(){
                        var config = {
                            'form_obj':$('#form_valid'),
                            'check_list':{
                                'password':Array('password'),
                                'password_again':Array('password','password_again')
                            },
                            'ajax_url':"{{ route('ajax_api') }}",
                        };
                        new M_valid(config);
                    });
                </script>
                <form id="form_valid" onSubmit="return false;" class="form-horizontal" role="form" action="" method="post" >
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">@lang('common.current')@lang('common.pass')</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" placeholder="@lang('common.current')@lang('common.pass')" name="cur_password" value="{{ $edit_info['admin_name'] }}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">@lang('common.new')@lang('common.pass')</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" placeholder="@lang('common.new')@lang('common.pass')" name="password" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">@lang('common.again')@lang('common.input')@lang('common.pass')</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" placeholder="@lang('common.again')@lang('common.input')@lang('common.pass')" name="password_again" />
                            @if ($edit_info)
                                <span class="help-block">@lang('common.not_input_pass')</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                @lang('common.save')
                            </button>
                            <a href="{{ route('Index/main') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
