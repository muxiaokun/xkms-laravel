<!DOCTYPE html>
<html>
    <head>
        <title><if condition="$title">{{ $title }} {{ trans('common.dash') }}</if> {$Think.APP_NAME}{{ trans('common.management') }}{{ trans('common.backend') }}</title>
        <link href="__ROOT__/Public/css/bimages/favicon.ico" type="image/ico" rel="shortcut icon" />
        <meta http-equiv="Content-Type" Content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <import type="css" file="css/jquery-ui#min" />
        <import type="css" file="css/jquery-ui#theme#min" />
        <import type="css" file="css/bootstrap#min" />
        <import type="css" file="css/bootstrap-theme#min" />
        <import type="css" file="css/common" />
        <import type="css" file="css/admin" />
        <import file="js/jquery#min" />
        <import file="js/bootstrap#min" />
        <import file="js/jquery-ui#min" />
        <import file="js/common" />
        <!--[if lt IE 10]>
            <import file="js/supporthtml5" />
        <![endif]-->
    </head>
    <body>
