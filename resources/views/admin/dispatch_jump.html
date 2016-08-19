<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{$Think.lang.system}{$Think.lang.info} {$Think.lang.dash} {:C('SITE_TITLE')}</title>
    <import type="css" file="css/bootstrap#min" />
    <import type="css" file="css/bootstrap-theme#min" />
    <import type="css" file="css/common" />
    <import type="css" file="css/admin" />
    <import file="js/jquery#min" />
    <!--[if lt IE 9]>
        <import file="js/supporthtml5" />
    <![endif]-->
</head>
<body>
    <section class="container">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="panel <if condition="$message">panel-info<else/>panel-danger</if> ">
                <div class="panel-heading text-center">
                    {$Think.lang.system}<if condition="$message">{$Think.lang.info}<else/>{$Think.lang.error}</if>
                </div>
                <div class="panel-body">
                    <div class="col-sm-4 text-center">
                        <if condition="$message">
                            <span class="glyphicon glyphicon-ok" style="color:green;font-size:80px;"></span>
                        <else/>
                            <span class="glyphicon glyphicon-warning-sign" style="color:red;font-size:80px;"></span>
                        </if>
                    </div>
                    <div class="col-sm-6  text-left" style="line-height: 40px;">
                        <span class="error"><?php echo($error); ?><?php echo($message); ?></span>
                        <p>页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        (function(){
        //两行居中代码
        var windowH = $(window).height(),obj = $('.container'),objH = obj.height(),marginTop = windowH/2 - objH;
        if(0 < marginTop)obj.css('margin-top',marginTop + 'px');
        
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                        location.href = href;
                        clearInterval(interval);
                };
        }, 1000);
        })();
    </script>
</body>
</html>