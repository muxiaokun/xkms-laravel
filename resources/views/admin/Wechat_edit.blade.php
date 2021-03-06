@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                <form method="post" class="form-horizontal" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $edit_info['id'] }}"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.send')@lang('common.to')</label>
                                <div class="col-sm-7">
                                    <input type="text" value="{{ $edit_info['member_name'] }}" class="form-control"
                                           disabled="disabled">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.start')@lang('common.content')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="start_content" value="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.start')@lang('common.content')@lang('common.color')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="start_content_color" value="#000000" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.end')@lang('common.content')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="end_content" value="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.end')@lang('common.content')@lang('common.color')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="end_content_color" value="#000000" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.content')1</label>
                                <div class="col-sm-7">
                                    <input type="text" name="content1" value="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.content')
                                    1 @lang('common.color')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="content1_color" value="#000000" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.content')2</label>
                                <div class="col-sm-7">
                                    <input type="text" name="content2" value="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.content')
                                    2 @lang('common.color')</label>
                                <div class="col-sm-7">
                                    <input type="text" name="content2_color" value="#000000" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">@lang('common.send')</button>
                            <input class="btn btn-default" type="reset" value="@lang('common.reset')">
                            <a href="{{ route('Admin::Wechat::index') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection