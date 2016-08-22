    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.website') }}{{ trans('common.title') }}<small>SITE_TITLE</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_TITLE" value="{:C('SITE_TITLE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.website') }}{{ trans('common.domain') }}<small>SITE_DOMAIN</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_DOMAIN" value="{:C('SITE_DOMAIN')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.company') }}{{ trans('common.name') }}<small>SITE_COMPANY</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_COMPANY" value="{:C('SITE_COMPANY')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.company') }}{{ trans('common.address') }}<small>SITE_ADDR</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_ADDR" value="{:C('SITE_ADDR')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.company') }}{{ trans('common.phone') }}<small>SITE_PHONE</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_PHONE" value="{:C('SITE_PHONE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.company') }}{{ trans('common.telphone') }}<small>SITE_TELPHONE</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_TELPHONE" value="{:C('SITE_TELPHONE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ICP{{ trans('common.icprecord') }}{{ trans('common.mark') }}<small>SITE_ICPNUMBER</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_ICPNUMBER" value="{:C('SITE_ICPNUMBER')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.other') }}{{ trans('common.info') }}<small>SITE_OTHER</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_OTHER" value="{:C('SITE_OTHER')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">SEO{{ trans('common.keywords') }}<small>SITE_KEYWORDS</small></label>
                            <div class="col-sm-8">
                                <textarea name="SITE_KEYWORDS" class="form-control" style="resize:none;height:100px;">{:C('SITE_KEYWORDS')}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">SEO{{ trans('common.description') }}<small>SITE_DESCRIPTION</small></label>
                            <div class="col-sm-8">
                                <textarea name="SITE_DESCRIPTION" class="form-control" style="resize:none;height:100px;">{:C('SITE_DESCRIPTION')}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('common.website') }}{{ trans('common.statistics') }}{{ trans('common.script') }}<small>SITE_SCRIPT</small></label>
                            <div class="col-sm-8">
                                <textarea name="SITE_SCRIPT" class="form-control" style="resize:none;height:100px;">{:C('SITE_SCRIPT')}</textarea>
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