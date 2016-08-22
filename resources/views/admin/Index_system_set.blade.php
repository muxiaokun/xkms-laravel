
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <div class="row">
                    {/*<!--时间格式-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.time') }}{{ trans('common.format') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_DATE" value="{:C('SYS_DATE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    {/*<!--细节时间格式-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.detail') }}{{ trans('common.time') }}{{ trans('common.format') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_DATE_DETAIL" value="{:C('SYS_DATE_DETAIL')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--数据调用自动缓存时间-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.database') }}{{ trans('common.cache') }}{{ trans('common.time') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="DATA_CACHE_TIME" value="{:C('DATA_CACHE_TIME')}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                    {/*<!--前台模版数据调用自动缓存时间-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Template{{ trans('common.cache') }}{{ trans('common.time') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_TD_CACHE" value="{:C('SYS_TD_CACHE')}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--最大页数-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.max') }}{{ trans('common.page') }}{{ trans('common.number') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_MAX_PAGE" value="{:C('SYS_MAX_PAGE')}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                    {/*<!--每页条数-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.page') }}{{ trans('common.max') }}{{ trans('common.rows') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_MAX_ROW" value="{:C('SYS_MAX_ROW')}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--默认图片-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.default') }}{{ trans('common.image') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_DEFAULT_IMAGE" value="{:C('SYS_DEFAULT_IMAGE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    {/*<!--异步图片-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.sync') }}{{ trans('common.loading') }}{{ trans('common.image') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_SYNC_IMAGE" value="{:C('SYS_SYNC_IMAGE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
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