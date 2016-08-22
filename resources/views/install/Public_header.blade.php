<!DOCTYPE html>
<html>
    <head>
        <title>{$title} {$Think.lang.dash} {$Think.APP_NAME}</title>
        <link href="__ROOT__/Public/css/bimages/favicon.ico" type="image/ico" rel="shortcut icon" />
        <meta http-equiv="Content-Type" Content="text/html;charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <import type="css" file="css/common" />
        <import type="css" file="css/bootstrap#min" />
        <import type="css" file="css/bootstrap-theme#min" />
        <import file="js/jquery#min" />
        <import file="js/bootstrap#min" />
        <import file="js/install" />
        <!--[if lt IE 9]>
            <import file="js/supporthtml5" />
        <![endif]-->
    </head>
    <body>
        <style>
            body{background-color: #2d527f;color:#FFF}
            .header_fixed{padding:0 30px;width:100%;height:100px;position:fixed;z-index: 1;background-color: #26384e;}
            .header_height{height:150px;}
            .footer_fixed{width:100%;height:80px;line-height:30px;position:fixed;bottom:0px;z-index: 1;background-color: #26384e;padding:10px 0;}
            .footer_height{height:150px;}
            .table-hover > tbody > tr:hover{color:#2d527f;} 
            .g-iframe {
                border: 0 none;
                height: 100%;
                left: 0;
                position: absolute;
                top: 0;
                width: 100%;
            }
        </style>
        <if condition="'index' neq ACTION_NAME">
        <div class="header_height">
        </div>
        </if>