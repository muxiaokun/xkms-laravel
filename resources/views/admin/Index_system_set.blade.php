
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <div class="row">
                    {{--时间格式--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.time')@lang('common.format')</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_DATE" value="{{ config('SYS_DATE') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    {{--细节时间格式--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.detail')@lang('common.time')@lang('common.format')</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_DATE_DETAIL" value="{{ config('SYS_DATE_DETAIL') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{--数据调用自动缓存时间--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.database')@lang('common.cache')@lang('common.time')</label>
                            <div class="col-sm-7">
                                <input type="text" name="DATA_CACHE_TIME" value="{{ config('DATA_CACHE_TIME') }}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                    {{--前台模版数据调用自动缓存时间--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Template@lang('common.cache')@lang('common.time')</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_TD_CACHE" value="{{ config('SYS_TD_CACHE') }}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{--最大页数--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.max')@lang('common.page')@lang('common.number')</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_MAX_PAGE" value="{{ config('SYS_MAX_PAGE') }}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                    {{--每页条数--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.page')@lang('common.max')@lang('common.rows')</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_MAX_ROW" value="{{ config('SYS_MAX_ROW') }}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{--默认图片--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.default')@lang('common.image')</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_DEFAULT_IMAGE" value="{{ config('SYS_DEFAULT_IMAGE') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    {{--异步图片--}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.sync')@lang('common.loading')@lang('common.image')</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_SYNC_IMAGE" value="{{ config('SYS_SYNC_IMAGE') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
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