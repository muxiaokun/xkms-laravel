        <script type="text/javascript">
            var progress = {:C('setp_progress.3')} - {:C('setp_progress.2')}; //上一步的
            var show_install_progress = function(percent)
            {
                if(parent && parent.move_progress)
                {
                    parent.move_progress(progress * percent + {:C('setp_progress.2')});
                }
            }
        </script>
        {/*<!--安装第三步界面 开始-->*/}
        <section class="container">
            <div class="row">
                <div class="col-sm-12 text-center"><div id="show_box" class="mt20"></div></div>
            </div>
        </section>
        {/*<!--安装第三步界面 结束-->*/}