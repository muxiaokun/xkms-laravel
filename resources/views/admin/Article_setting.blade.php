
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <div class="row">
                    {/*<!--是否异步加载图片-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.sync') }}{{ trans('common.content') }}{{ trans('common.image') }}</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_ARTICLE_SYNC_IMAGE" value="1" <if condition="'1' heq C('SYS_ARTICLE_SYNC_IMAGE')">checked="checked"</if> >{{ trans('common.enable') }}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="SYS_ARTICLE_SYNC_IMAGE" value="0" <if condition="'0' heq C('SYS_ARTICLE_SYNC_IMAGE')">checked="checked"</if> >{{ trans('common.disable') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    {/*<!--文章前后数量-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.before') }}{{ trans('common.later') }}{{ trans('common.piece') }}{{ trans('common.limit') }}</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_ARTICLE_PN_LIMIT" value="{{ config('SYS_ARTICLE_PN_LIMIT') }}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {/*<!--文章缩略图-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.article') }}{{ trans('common.thumb') }}{{ trans('common.width') }}PX</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_ARTICLE_THUMB_WIDTH" value="{{ config('SYS_ARTICLE_THUMB_WIDTH') }}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                    {/*<!--文章缩略图-->*/}
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('common.article') }}{{ trans('common.thumb') }}{{ trans('common.height') }}PX</label>
                            <div class="col-sm-7">
                                <input type="text" name="SYS_ARTICLE_THUMB_HEIGHT" value="{{ config('SYS_ARTICLE_THUMB_HEIGHT') }}" class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">{{ trans('common.save') }}</button>
                        <input class="btn btn-default" type="reset" value="{{ trans('common.reset') }}">
                        <a href="{{ route('main') }}" class="btn btn-default">
                            {{ trans('common.goback') }}
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>