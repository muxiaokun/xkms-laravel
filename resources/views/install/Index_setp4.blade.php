        <script type="text/javascript">
            $(function(){
                if(parent && parent.move_progress)
                {
                    parent.move_progress({{ config('setp_progress.4') }});
                }
            });
        </script>
        {/*<!--安装第四步界面 开始-->*/}
        <section class="container">
            <div class="row">
                <div class="col-sm-12">
                     {{ $article }}
                </div>
                <div class="col-sm-3 col-sm-offset-3 text-center">
                    <a class="btn btn-lg btn-primary" target="_parent" href="{{ route(C(DEFAULT_MODULE).'/'.C(DEFAULT_CONTROLLER).'/'.C(DEFAULT_ACTION)) }}">
                        {{ trans('common.setp4_1') }}
                    </a>
                </div>
                <div class="col-sm-3 text-center">
                    <a class="btn btn-lg btn-primary" target="_parent" href="{{ route('Admin/'.C(DEFAULT_CONTROLLER).'/'.C(DEFAULT_ACTION)) }}">
                        {{ trans('common.setp4_2') }}
                    </a>
                </div>
            </div>
        </section>
        {/*<!--安装第四步界面 结束-->*/}