@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form method="post" class="form-horizontal" role="form">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.website')@lang('common.title')
                                    <small>&nbsp;&nbsp;site_title</small>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="site_title" value="{{ config('website.site_title') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.website')@lang('common.domain')
                                    <small>site_domain</small>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="site_domain" value="{{ config('website.site_domain') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.company')@lang('common.name')
                                    <small>site_company</small>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="site_company" value="{{ config('website.site_company') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.company')@lang('common.address')
                                    <small>site_addr</small>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="site_addr" value="{{ config('website.site_addr') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.company')@lang('common.phone')
                                    <small>site_phone</small>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="site_phone" value="{{ config('website.site_phone') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.company')@lang('common.telphone')
                                    <small>site_telphone</small>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="site_telphone"
                                           value="{{ config('website.site_telphone') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">ICP @lang('common.icprecord')@lang('common.mark')
                                    <small>site_icpnumber</small>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="site_icpnumber"
                                           value="{{ config('website.site_icpnumber') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.other')@lang('common.info')
                                    <small>site_other</small>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="site_other" value="{{ config('website.site_other') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">SEO @lang('common.keywords')
                                    <small>site_keywords</small>
                                </label>
                                <div class="col-sm-8">
                                <textarea name="site_keywords" class="form-control"
                                          style="resize:none;height:100px;">{{ config('website.site_keywords') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">SEO @lang('common.description')
                                    <small>site_description</small>
                                </label>
                                <div class="col-sm-8">
                                <textarea name="site_description" class="form-control"
                                          style="resize:none;height:100px;">{{ config('website.site_description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">@lang('common.website')@lang('common.statistics')@lang('common.script')
                                    <small>site_script</small>
                                </label>
                                <div class="col-sm-8">
                                <textarea name="site_script" class="form-control"
                                          style="resize:none;height:100px;">{{ config('website.site_script') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
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