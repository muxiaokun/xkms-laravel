@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form method="post" class="form-horizontal" role="form">
                    {{ csrf_field() }}
                    <div class="row">
                        {{--是否异步加载图片--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.sync')@lang('common.content')@lang('common.image')</label>
                                <div class="col-sm-7">
                                    <label class="radio-inline">
                                        <input type="radio" name="sys_article_sync_image" value="1"
                                               @if ('1' === config('system.sys_article_sync_image'))checked="checked"@endif >@lang('common.enable')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="sys_article_sync_image" value="0"
                                               @if ('0' === config('system.sys_article_sync_image'))checked="checked"@endif >@lang('common.disable')
                                    </label>
                                </div>
                            </div>
                        </div>
                        {{--文章前后数量--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.before')@lang('common.later')@lang('common.piece')@lang('common.limit')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="sys_article_pn_limit"
                                           value="{{ config('system.sys_article_pn_limit') }}" class="form-control"
                                           onKeyup="M_in_int(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{--文章缩略图--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.article')@lang('common.thumb')@lang('common.width')
                                    PX</label>
                                <div class="col-sm-7">
                                    <input type="text" name="sys_article_thumb_width"
                                           value="{{ config('system.sys_article_thumb_width') }}" class="form-control"
                                           onKeyup="M_in_int(this);">
                                </div>
                            </div>
                        </div>
                        {{--文章缩略图--}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.article')@lang('common.thumb')@lang('common.height')
                                    PX</label>
                                <div class="col-sm-7">
                                    <input type="text" name="sys_article_thumb_height"
                                           value="{{ config('system.sys_article_thumb_height') }}" class="form-control"
                                           onKeyup="M_in_int(this);">
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