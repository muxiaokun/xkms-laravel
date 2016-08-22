    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{$title}</div>
            <div class="panel-body">
            <form method="post" class="form-horizontal"  role="form">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.website}{$Think.lang.title}<small>SITE_TITLE</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_TITLE" value="{:C('SITE_TITLE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.website}{$Think.lang.domain}<small>SITE_DOMAIN</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_DOMAIN" value="{:C('SITE_DOMAIN')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.company}{$Think.lang.name}<small>SITE_COMPANY</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_COMPANY" value="{:C('SITE_COMPANY')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.company}{$Think.lang.address}<small>SITE_ADDR</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_ADDR" value="{:C('SITE_ADDR')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.company}{$Think.lang.phone}<small>SITE_PHONE</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_PHONE" value="{:C('SITE_PHONE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.company}{$Think.lang.telphone}<small>SITE_TELPHONE</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_TELPHONE" value="{:C('SITE_TELPHONE')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ICP{$Think.lang.icprecord}{$Think.lang.mark}<small>SITE_ICPNUMBER</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_ICPNUMBER" value="{:C('SITE_ICPNUMBER')}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.other}{$Think.lang.info}<small>SITE_OTHER</small></label>
                            <div class="col-sm-8">
                                <input type="text" name="SITE_OTHER" value="{:C('SITE_OTHER')}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">SEO{$Think.lang.keywords}<small>SITE_KEYWORDS</small></label>
                            <div class="col-sm-8">
                                <textarea name="SITE_KEYWORDS" class="form-control" style="resize:none;height:100px;">{:C('SITE_KEYWORDS')}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">SEO{$Think.lang.description}<small>SITE_DESCRIPTION</small></label>
                            <div class="col-sm-8">
                                <textarea name="SITE_DESCRIPTION" class="form-control" style="resize:none;height:100px;">{:C('SITE_DESCRIPTION')}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$Think.lang.website}{$Think.lang.statistics}{$Think.lang.script}<small>SITE_SCRIPT</small></label>
                            <div class="col-sm-8">
                                <textarea name="SITE_SCRIPT" class="form-control" style="resize:none;height:100px;">{:C('SITE_SCRIPT')}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">{$Think.lang.save}</button>
                        <input class="btn btn-default" type="reset" value="{$Think.lang.reset}">
                        <a href="{:U('main')}" class="btn btn-default">
                            {$Think.lang.goback}
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </section>