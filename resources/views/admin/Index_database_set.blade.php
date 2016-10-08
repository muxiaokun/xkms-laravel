
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.host')</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_HOST" value="{{ config('DB_HOST') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.database')</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_NAME" value="{{ config('DB_NAME') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.user')</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_USER" value="{{ config('DB_USER') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.pass')</label>
                            <div class="col-sm-5">
                                <input type="password" name="DB_PWD" class="form-control">
                            </div>
                            @if (0 lt strlen(C('DB_PWD')))
                                <div class="col-sm-4" style="color:green;">@lang('common.exists')@lang('common.pass')</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.port')</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_PORT" value="{{ config('DB_PORT') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.prefix')</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_PREFIX" value="{{ config('DB_PREFIX') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.backup')@lang('common.database')</label>
                            <div class="col-sm-7">
<a class="btn btn-sm btn-success" href="{{ route('database_set',array('backup'=>'1')) }}" target="_blank">@lang('common.download')@lang('common.database')@lang('common.backup')</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.restore')@lang('common.database')</label>
                            <div class="col-sm-7">
                                <input type="file" name="restore_file" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-offset-5">
                        <button type="submit" class="btn btn-info">@lang('common.save')</button>
                        <input class="btn btn-default" type="reset" value="@lang('common.reset')">
                        <a href="{{ route('main') }}" class="btn btn-default">
                            @lang('common.goback')
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>