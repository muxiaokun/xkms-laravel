<!DOCTYPE html>
<html>
    <head>
        <title><if condition="$title">{$title} {$Think.lang.dash}</if> {:C('SITE_TITLE')}</title>
        <link href="__ROOT__/favicon.ico" type="image/ico" rel="shortcut icon" />
        <meta http-equiv="Content-Type" Content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <if condition="C('SITE_KEYWORDS')"><meta name="keywords" content="{:C('SITE_KEYWORDS')}" /></if>
        <if condition="C('SITE_KEYWORDS')"><meta name="description" content="{:C('SITE_DESCRIPTION')}" /></if>
        <if condition="L('pfcopyright')"><meta name="author" content="{:L('pfcopyright',array('app_name'=>APP_NAME))}" /></if>
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
    </head>
    <body>
        <header class="container">
            <div data-ride="carousel" class="carousel slide" id="carousel-example-captions">
                <ol class="carousel-indicators">
                    <li class="active" data-slide-to="0" data-target="#carousel-example-captions"></li>
                    <li data-slide-to="1" data-target="#carousel-example-captions" class=""></li>
                </ol>
                <div role="listbox" class="carousel-inner">
                    <div class="item active">
                        <img alt="" src="{:M_exists('Uploads/attached/image/index/banner1.png')}" />
                        <div class="carousel-caption">
                            <h3></h3>
                            <p></p>
                        </div>
                    </div>
                    <div class="item">
                        <img alt="" src="{:M_exists('Uploads/attached/image/index/banner2.png')}" />
                        <div class="carousel-caption">
                            <h3></h3>
                            <p></p>
                        </div>
                    </div>
                </div>
                <a data-slide="prev" role="button" href="#carousel-example-captions" class="left carousel-control">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a data-slide="next" role="button" href="#carousel-example-captions" class="right carousel-control">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
            {/*导航示例 开始*/}
            <M:D item="nav_menu" name="Navigation" fn="m_find_data" fn_arg="nav_menu" />
            <nav class="navbar navbar-default" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                          <span class="sr-only">Toggle navigation</span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="{$nav_menu[0]['nav_url']}"><b>{$nav_menu[0]['nav_text']}</b></a>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <foreach name="nav_menu" item="data">
                                <if condition="0 lt $key">
                                <li class="<if condition="$data['nav_active']">active</if>">
                                    <if condition="$data['nav_child']">
                                        <a  data-toggle="dropdown" href="#"><b>{$data.nav_text}</b><span class="caret"></span></a>
                                        <ul class="dropdown-menu" role="menu">
                                            <foreach name="data.nav_child" item="child_data">
                                                <li><a href="{$child_data.nav_url}" target="{$child_data.nav_target}" ><b>{$child_data.nav_text}</b></a></li>
                                            </foreach>
                                        </ul>
                                    <else />
                                        <a href="{$data.nav_url}" target="{$data.nav_target}" ><b>{$data.nav_text}</b></a>
                                    </if>
                                </li>
                                </if>
                            </foreach>
                        </ul>
                        <form class="navbar-form navbar-right" role="search" action="{:M_U('Article/search')}" method="post">
                            <if condition="isset($category_position['id'])">
                                <input type="hidden" name="cate_id" value="{$category_position.id}">
                            </if>
                            <div class="form-group">
                                <select name="type" class="form-control">
                                    <option value="title" <if condition="$request['type'] eq 'type'">selected="selected"</if> >{$Think.lang.search}{$Think.lang.type}</option>
                                    <option value="description" <if condition="$request['type'] eq 'description'">selected="selected"</if>>{$Think.lang.description}</option>
                                    <option value="content" <if condition="$request['type'] eq 'content'">selected="selected"</if>>{$Think.lang.content}</option>
                                    <if condition="isset($category_position['extend'])">
                                    <foreach name="category_position['extend']" item="extend">
                                        <option value="extend[{$extend}]" <if condition="$request['type'] eq 'extend['.$extend.']'">selected="selected"</if>>L({$extend})</option>
                                    </foreach>
                                    </if>
                                    <option value="all">{$Think.lang.all}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input name="keyword" value="{$request['keyword']}" type="text" class="form-control" placeholder="{$Think.lang.keywords}">
                            </div>
                            <button type="submit" class="btn btn-default"><b>{$Think.lang.search}</b></button>
                        </form>
                    </div>
                </div>
            </nav>
            {/*导航示例 结束*/}
        </header>