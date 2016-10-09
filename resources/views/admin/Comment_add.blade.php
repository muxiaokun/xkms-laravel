<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            <form method="post" class="form-horizontal" role="form">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.yes')@lang('common.no')@lang('common.enable')</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="COMMENT_SWITCH" value="1"
                                           @if ('1' heq C('COMMENT_SWITCH'))checked="checked"@endif >@lang('common.enable')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="COMMENT_SWITCH" value="0"
                                           @if ('0' heq C('COMMENT_SWITCH'))checked="checked"@endif >@lang('common.disable')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.permit')@lang('common.controller')
                                A,B</label>
                            <div class="col-sm-7">
                                <input type="text" name="COMMENT_ALLOW" value="{{ config('COMMENT_ALLOW') }}"
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
                                    <input type="radio" name="COMMENT_ANONY" value="1"
                                           @if ('1' heq C('COMMENT_ANONY'))checked="checked"@endif >@lang('common.enable')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="COMMENT_ANONY" value="0"
                                           @if ('0' heq C('COMMENT_ANONY'))checked="checked"@endif >@lang('common.disable')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('common.comment')@lang('common.time')@lang('common.interval')
                                (@lang('common.second'))</label>
                            <div class="col-sm-7">
                                <input type="text" name="COMMENT_INTERVAL" value="{{ config('COMMENT_INTERVAL') }}"
                                       class="form-control" onKeyup="M_in_int(this);">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-info">@lang('common.save')</button>
                        <input class="btn btn-default" type="reset" value="@lang('common.reset')">
                        <a href="{{ route('index') }}" class="btn btn-default">
                            @lang('common.goback')
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>