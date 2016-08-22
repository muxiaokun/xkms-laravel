<?php
// Empty Controller 空模块控制器
// 为了小朋友能够早日回家 请不要修改
// 该页面只会在链接错误 访问不到文件时才会出现 10秒后自动跳转 如果你有爱心可以加大秒数

namespace App\Http\Controllers;

class CommonEmpty extends Controller
{
    public function _empty()
    {
        if (APP_DEBUG) // && false
        {
            echo "MODULE_NAME:" . MODULE_NAME . "<br />";
            echo "CONTROLLER_NAME:" . CONTROLLER_NAME . "<br />";
            echo "ACTION_NAME:" . ACTION_NAME . "<br />";
            dump($_REQUEST);
        } else {
            $holdTime = 10;
            //小于10秒 该公益页面也就没有意义了 将自动跳回默认页面
            $appName = APP_NAME;
            $root     = __ROOT__;
            $html     = <<< EOF
<!DOCTYPE>
<html><head>
<title> 网页没有找到 404 —— {$appName}</title>
<base href="{$root}/" />
<meta http-equiv="Content-Type" Content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="Public/css/bootstrap.min.css" /></head><body>
<section class="container">
    <!-- 设置了系统的404页面 -->
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3" style="margin-top: 13%;">
            <img style="width:100%;" src="Public/css/bimages/404.png" />
        </div>
    </div>
    <div class="row text-center"><p>页面自动 <a id="href" href="javascript:history.go(-1);">跳转</a> 等待时间： <b id="wait">{$holdTime}</b></p></div>
</section>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait');
        if(10 < wait)mJumpLink();
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                clearInterval(interval);
                history.go(-1);
            };
        }, 1000);
    })();
</script>
</body></html>
EOF;
            echo $html;
        }

    }
}
