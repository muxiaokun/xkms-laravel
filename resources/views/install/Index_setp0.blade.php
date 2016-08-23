@extends('install.layout')
@section('body')
        <script type="text/javascript">
            $(function(){
                var doc = $(document);
                doc.on('scroll',function(event,a1,a2){
                    var progress = doc.scrollTop()/doc.height() * {{ config('install.setp_progress.0') }};
                    if(parent && parent.move_progress)
                    {
                        parent.move_progress(progress,{{ config('install.setp_progress.1') }});
                    }
                });
            });
        </script>
        {{-- 安装初始界面 开始 --}}
        <section class="container">
            <div class="row">
                <div class="col-sm-12">
                    <pre>{{ $article }}</pre>
                </div>
                <div class="col-sm-12 text-center">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" id="checkGnu" />{{ trans('install.setp0_commont1') }}
                        </label>
                    </div>
                    <a class="btn btn-lg btn-primary" onClick="return check_checkBoxVal('#checkGnu','{{ trans('install.setp0_commont2') }}')" href="{{ route('Install::setp1') }}">
                        {{ trans('install.setp0') }}
                    </a>
                </div>
            </div>
        </section>
        {{-- 安装初始界面 结束 --}}
@endsection