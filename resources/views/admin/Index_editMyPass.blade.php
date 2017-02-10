@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form id="form_valid" onSubmit="return false;" class="form-horizontal" role="form" action=""
                      method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">@lang('common.current')@lang('common.pass')</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control"
                                   placeholder="@lang('common.current')@lang('common.pass')" name="cur_password"
                                   value=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">@lang('common.new')@lang('common.pass')</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control"
                                   placeholder="@lang('common.new')@lang('common.pass')" name="password"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label">@lang('common.again')@lang('common.input')@lang('common.pass')</label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control"
                                   placeholder="@lang('common.again')@lang('common.input')@lang('common.pass')"
                                   name="password_confirmation"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">
                                @lang('common.save')
                            </button>
                            <a href="{{ route('Admin::Index::main') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script type="text/javascript" src="{{ asset('js/M_valid.js') }}"></script>
<script>
    $(function () {
        var config = {
            'form_obj': $('#form_valid'),
            'check_list': {
                'password': Array('password'),
                'password_confirmation': Array('password', 'password_confirmation')
            },
            'ajax_url': "{{ route('Admin::Index::ajax_api') }}",
        };
        new M_valid(config);
    });
</script>
@endpush