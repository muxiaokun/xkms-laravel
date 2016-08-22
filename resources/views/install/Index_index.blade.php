
        <header class="header_fixed">
            <div class="row">
                <div class="col-sm-12">
                    <h3>{$Think.lang.install}{$Think.lang.schedule}{$Think.lang.colon}{$setp}</h3>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%">
                            <span class="sr-only">{$progress}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <script type="text/javascript">
            var move_progress = function(num)
            {
                var progress_bar = $('.progress-bar');
                if(0 == progress_bar.length)return;
                progress_bar.css('width',num + '%');
                var sr_only = progress_bar.find('.sr-only');
                if(0 == sr_only.length)return;
                sr_only.html(num + '%');
            }
            $(function(){
            })
        </script>
        <iframe id="main" name="main" class="g-iframe" src="{:U('setp0')}" width="100%" height="100%" scrolling="auto">
        </iframe>
        <footer class="footer_fixed text-center">
            {:L('pfcopyright',array('app_name'=>APP_NAME))}<br />
            {$Think.lang.version}{$Think.lang.colon} Install 1.8.0
        </footer>