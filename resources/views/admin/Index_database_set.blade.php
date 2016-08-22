
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.host') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_HOST" value="{:C('DB_HOST')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.database') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_NAME" value="{:C('DB_NAME')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.user') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_USER" value="{:C('DB_USER')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.pass') }}</label>
                            <div class="col-sm-5">
                                <input type="password" name="DB_PWD" class="form-control">
                            </div>
                            <if condition="0 lt strlen(C('DB_PWD'))">
                                <div class="col-sm-4" style="color:green;">{{ trans('common.exists') }}{{ trans('common.pass') }}</div>
                            </if>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.port') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_PORT" value="{:C('DB_PORT')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.prefix') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DB_PREFIX" value="{:C('DB_PREFIX')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.backup') }}{{ trans('common.database') }}</label>
                            <div class="col-sm-7">
<a class="btn btn-sm btn-success" href="{:U('database_set',array('backup'=>'1'))}" target="_blank">{{ trans('common.download') }}{{ trans('common.database') }}{{ trans('common.backup') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.restore') }}{{ trans('common.database') }}</label>
                            <div class="col-sm-7">
                                <input type="file" name="restore_file" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-offset-5">
                        <button type="submit" class="btn btn-info">{{ trans('common.save') }}</button>
                        <input class="btn btn-default" type="reset" value="{{ trans('common.reset') }}">
                        <a href="{:U('main')}" class="btn btn-default">
                            {{ trans('common.goback') }}
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>