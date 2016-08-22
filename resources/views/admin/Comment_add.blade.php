
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.yes') }}{{ trans('common.no') }}{{ trans('common.enable') }}</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="COMMENT_SWITCH" value="1" <if condition="'1' heq C('COMMENT_SWITCH')">checked="checked"</if> >{{ trans('common.enable') }}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="COMMENT_SWITCH" value="0" <if condition="'0' heq C('COMMENT_SWITCH')">checked="checked"</if> >{{ trans('common.disable') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.permit') }}{{ trans('common.controller') }} A,B</label>
                            <div class="col-sm-7">
                                <input type="text" name="COMMENT_ALLOW" value="{:C('COMMENT_ALLOW')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.enable') }}{{ trans('common.anonymous') }}{{ trans('common.comment') }}</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="COMMENT_ANONY" value="1" <if condition="'1' heq C('COMMENT_ANONY')">checked="checked"</if> >{{ trans('common.enable') }}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="COMMENT_ANONY" value="0" <if condition="'0' heq C('COMMENT_ANONY')">checked="checked"</if> >{{ trans('common.disable') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.comment') }}{{ trans('common.time') }}{{ trans('common.interval') }}({{ trans('common.second') }})</label>
                            <div class="col-sm-7">
                                <input type="text" name="COMMENT_INTERVAL" value="{:C('COMMENT_INTERVAL')}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">{{ trans('common.save') }}</button>
                        <input class="btn btn-default" type="reset" value="{{ trans('common.reset') }}">
                        <a href="{:U('index')}" class="btn btn-default">
                            {{ trans('common.goback') }}
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>