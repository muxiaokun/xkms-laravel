@extends('install.layout')
@section('body')
    {{-- 安装初始界面 开始 --}}
    <section class="container">
        <div class="row">
            <div class="col-sm-12">
                <pre>{{ $licenses }}</pre>
            </div>
            <div class="col-sm-12 text-center">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="1" id="checkGnu"/>@lang('install.setp0_commont1')
                    </label>
                </div>
                <a class="btn btn-lg btn-primary"
                   onClick="return check_checkBoxVal('#checkGnu','@lang('install.setp0_commont2')')"
                   href="{{ route('Install::setp1') }}">
                    @lang('install.setp0')
                </a>
            </div>
        </div>
    </section>
    {{-- 安装初始界面 结束 --}}
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        var win = $(window);
        var doc = $(document);
        doc.on('scroll', function (event, a1, a2) {
            if (parent && parent.move_progress) {
                var progress = doc.scrollTop() / (doc.height() - win.height()) * {{ config('install.setp_progress.0') }};
                parent.move_progress(progress,{{ config('install.setp_progress.1') }});
            }
        });
    });
</script>
@endpush