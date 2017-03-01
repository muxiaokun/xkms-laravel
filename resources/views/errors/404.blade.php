<!DOCTYPE>
<html>
<head>
    <title> 网页没有找到 404 —— @lang('common.app_name')</title>
    <meta http-equiv="Content-Type" Content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}"/>
</head>
<body>
<section class="container">
    <!-- 设置了系统的404页面 -->
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3" style="margin-top: 13%;">
            <img style="width:100%;" src="{{ asset('css/bimages/404.png') }}"/>
        </div>
    </div>
    <div class="row text-center"><p>页面自动 <a id="href" href="javascript:history.go(-1);">跳转</a> 等待时间： <b id="wait">5</b>
        </p></div>
</section>
<script type="text/javascript">
    (function () {
        var wait = document.getElementById('wait');
        if (10 < wait) mJumpLink();
        var interval = setInterval(function () {
            var time = --wait.innerHTML;
            if (time <= 0) {
                clearInterval(interval);
                history.go(-1);
            }
            ;
        }, 1000);
    })();
</script>
</body>
</html>