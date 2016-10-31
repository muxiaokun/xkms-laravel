@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.host')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="db_host" value="{{ env('DB_HOST') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.database')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="db_database" value="{{ env('DB_DATABASE') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.user')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="db_username" value="{{ env('DB_USERNAME') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.pass')</label>
                                <div class="col-sm-4">
                                    <input type="password" name="db_password" class="form-control">
                                </div>
                                <div class="col-sm-4 checkbox">
                                    <label>
                                        <input type="checkbox" name="edit_password" value="1">@lang('common.edit')@lang('common.pass')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.port')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="db_port" value="{{ env('DB_PORT') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.prefix')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="db_prefix" value="{{ env('DB_PREFIX') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.backup')@lang('common.database')</label>
                                <div class="col-sm-7">
                                    <a class="btn btn-sm btn-success"
                                       href="{{ route('Admin::Index::databaseSet',array('backup'=>'1')) }}"
                                       target="_blank">@lang('common.download')@lang('common.database')@lang('common.backup')</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.restore')@lang('common.database')</label>
                                <div class="col-sm-7">
                                    <input type="file" name="restore_file"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-5">
                            <button type="submit" class="btn btn-info">@lang('common.save')</button>
                            <input class="btn btn-default" type="reset" value="@lang('common.reset')">
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