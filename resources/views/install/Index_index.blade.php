@extends('install.layout')
@section('body')
    <header class="header_fixed">
        <div class="row">
            <div class="col-sm-12">
                <h3>@lang('common.install')@lang('common.schedule')@lang('common.colon') {{ $setp }}</h3>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40"
                         aria-valuemin="0" aria-valuemax="100" style="width: {{ $progress }}%">
                        <span class="sr-only">{{ $progress }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <script type="text/javascript">
        var move_progress = function (num) {
            var progress_bar = $('.progress-bar');
            if (0 == progress_bar.length)return;
            progress_bar.css('width', num + '%');
            var sr_only = progress_bar.find('.sr-only');
            if (0 == sr_only.length)return;
            sr_only.html(num + '%');
        }
        $(function () {
        })
    </script>
    <iframe id="main" name="main" class="g-iframe" src="{{ route('Install::setp0') }}" width="100%" height="100%"
            scrolling="auto">
    </iframe>
    <footer class="footer_fixed text-center">
        @lang('common.pfcopyright',['app_name'=>trans('common.app_name')])<br/>
        @lang('common.version')@lang('common.colon') 2.0.0
    </footer>
@endsection