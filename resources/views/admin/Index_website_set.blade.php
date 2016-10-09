<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            <form method="post" class="form-horizontal" role="form">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.website')@lang('common.title')
                                <small>SITE_TITLE</small>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_TITLE" value="{{ config('website.site_title') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.website')@lang('common.domain')
                                <small>SITE_DOMAIN</small>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_DOMAIN" value="{{ config('website.site_domain') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.company')@lang('common.name')
                                <small>SITE_COMPANY</small>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_COMPANY" value="{{ config('website.site_company') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.company')@lang('common.address')
                                <small>SITE_ADDR</small>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_ADDR" value="{{ config('website.site_addr') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.company')@lang('common.phone')
                                <small>SITE_PHONE</small>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_PHONE" value="{{ config('website.site_phone') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.company')@lang('common.telphone')
                                <small>SITE_TELPHONE</small>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_TELPHONE" value="{{ config('website.site_telphone') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ICP@lang('common.icprecord')@lang('common.mark')
                                <small>SITE_ICPNUMBER</small>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_ICPNUMBER" value="{{ config('website.site_icpnumber') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.other')@lang('common.info')
                                <small>SITE_OTHER</small>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_OTHER" value="{{ config('website.site_other') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">SEO@lang('common.keywords')
                                <small>SITE_KEYWORDS</small>
                            </label>
                            <div class="col-sm-8">
                                <textarea name="SITE_KEYWORDS" class="form-control"
                                          style="resize:none;height:100px;">{{ config('website.site_keywords') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">SEO@lang('common.description')
                                <small>SITE_DESCRIPTION</small>
                            </label>
                            <div class="col-sm-8">
                                <textarea name="SITE_DESCRIPTION" class="form-control"
                                          style="resize:none;height:100px;">{{ config('website.site_description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">@lang('common.website')@lang('common.statistics')@lang('common.script')
                                <small>SITE_SCRIPT</small>
                            </label>
                            <div class="col-sm-8">
                                <textarea name="SITE_SCRIPT" class="form-control"
                                          style="resize:none;height:100px;">{{ config('website.site_script') }}</textarea>
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