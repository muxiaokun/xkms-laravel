@extends('install.layout')
@section('body')
    {{--安装第四步界面 开始--}}
    <section class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="jumbotron" style="background:none;"><h2>感谢您使用 @lang('common.app_name')</h2>
                    <p>如果您发现了Bug或者不合理的流程请联系开发者 E-mail:test20121212@qq.com</p></div>
            </div>
            <div class="col-sm-3 col-sm-offset-3 text-center">
                <a class="btn btn-lg btn-primary" target="_parent"
                   href="{{ route('Home::Index::index') }}">
                    @lang('install.setp4_1')
                </a>
            </div>
            <div class="col-sm-3 text-center">
                <a class="btn btn-lg btn-primary" target="_parent"
                   href="{{ route('Admin::Index::index') }}">
                    @lang('install.setp4_2')
                </a>
            </div>
        </div>
    </section>
    {{--安装第四步界面 结束--}}
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        if (parent && parent.move_progress) {
            parent.move_progress({{ config('install.setp_progress.4') }});
        }
    });
</script>
@endpush