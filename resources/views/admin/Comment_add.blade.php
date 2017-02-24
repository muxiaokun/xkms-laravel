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
                                <label class="col-sm-4 control-label">@lang('common.yes')@lang('common.no')@lang('common.enable')</label>
                                <div class="col-sm-7">
                                    <label class="radio-inline">
                                        <input type="radio" name="comment_switch" value="1"
                                               @if ('1' === config('system.comment_switch'))checked="checked"@endif >@lang('common.enable')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="comment_switch" value="0"
                                               @if ('0' === config('system.comment_switch'))checked="checked"@endif >@lang('common.disable')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.permit')@lang('common.privilege')
                                    A,B</label>
                                <div class="col-sm-7">
                                    <input type="text" name="comment_allow" value="{{ config('system.comment_allow') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.enable')@lang('common.anonymous')@lang('common.comment')</label>
                                <div class="col-sm-7">
                                    <label class="radio-inline">
                                        <input type="radio" name="comment_anony" value="1"
                                               @if ('1' === config('system.comment_anony'))checked="checked"@endif >@lang('common.enable')
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="comment_anony" value="0"
                                               @if ('0' === config('system.comment_anony'))checked="checked"@endif >@lang('common.disable')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">@lang('common.comment')@lang('common.time')@lang('common.interval')
                                    (@lang('common.second'))</label>
                                <div class="col-sm-7">
                                    <input type="text" name="comment_interval"
                                           value="{{ config('system.comment_interval') }}"
                                           class="form-control" onKeyup="M_in_int(this);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info">@lang('common.save')</button>
                            <input class="btn btn-default" type="reset" value="@lang('common.reset')">
                            <a href="{{ route('Admin::Comment::index') }}" class="btn btn-default">
                                @lang('common.goback')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection