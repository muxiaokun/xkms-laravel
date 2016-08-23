{__NOLAYOUT__}
<!DOCTYPE html>
<html>
    <head>
        <title><if condition="$title">{{ $title }} {{ trans('common.dash') }}</if> {{ config('SITE_TITLE') }}</title>
        <link href="__ROOT__/favicon.ico" type="image/ico" rel="shortcut icon" />
        <meta http-equiv="Content-Type" Content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="keywords" content="{{ config('SITE_KEYWORDS') }}" />
        <meta name="description" content="{{ config('SITE_DESCRIPTION') }}" />
        <meta name="author" content="{:L('pfcopyright',array('app_name'=>APP_NAME))}" />
        <import type="css" file="css/jquery-ui#min" />
        <import type="css" file="css/bootstrap#min" />
        <import type="css" file="css/bootstrap-theme#min" />
        <import type="css" file="css/common" />
        <import type="css" file="css/home" />
        <import file="js/jquery#min" />
        <import file="js/bootstrap#min" />
        <import file="js/jquery-ui#min" />
        <import file="js/common" />
        <!--[if lt IE 10]>
            <import file="js/supporthtml5" />
        <![endif]-->
        <!--[if lt IE 10]><import file="js/supporthtml5" /><![endif]-->
    </head>
    <body>
        <section class="container">
            <div class="w1000 h400"><M:Img src="{:M_exists('Uploads/attached/image/index/banner1.png')}" /></div>
            <div class="w1000 h400"><M:Img src="Uploads/attached/image/index/banner2.png" /></div>
            <div class="w1000 h400"><M:Img src="Uploads/attached/image/index/banner1.png" /></div>
            <div class="w1000 h400"><M:Img src="Uploads/attached/image/index/banner2.png" /></div>
        </section>
        <section class="container hidden">
            <div class="jumbotron">
                <h1>{$Think.const.APP_NAME}</h1>
                <p>{{ trans('common.version') }}{{ trans('common.colon') }} Home Module 1.0.0</p>
                <p><a class="btn btn-primary btn-lg" role="button" href="http://www.xjhywh.cn" target="_blank">Learn more</a></p>
            </div>
            <ul class="list-group">
                <li class="list-group-item"><a href="{:M_U('Member/index')}">{{ trans('common.member') }}</a></li>
                <li class="list-group-item"><a href="{:M_U('Quests/index')}">问卷</a></li>
                <li class="list-group-item"><a href="{:M_U('Assess/index')}">考核</a></li>
                <li class="list-group-item"><a href="{:M_U('MessageBoard/index')}">留言板</a></li>
            </ul>
        </section>
    </body>
</html>