<!DOCTYPE html>
<html>
<head>
    <title>@if (isset($title)){{ $title }} @lang('common.dash')@endif {{ config('website.site_title') }}</title>
    <link href="{{ asset('css/bimages/favicon.ico') }}" type="image/ico" rel="shortcut icon"/>
    <meta http-equiv="Content-Type" Content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    @if (config('system.site_keywords'))
        <meta name="keywords" content="{{ config('website.site_keywords') }}"/>@endif
    @if (config('system.site_keywords'))
        <meta name="description" content="{{ config('website.site_description') }}"/>@endif
    <meta name="author" content="{{ trans('common.pfcopyright',['app_name'=>trans('common.app_name')]) }}"/>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-theme.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
    <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
    <!--[if lt IE 10]>
    <script type="text/javascript" src="{{ asset('js/supporthtml5.js') }}"></script>
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
                <img alt="" src="{{ mExists('css/fimages/banner1.png') }}"/>
                <div class="carousel-caption">
                    <h3></h3>
                    <p></p>
                </div>
            </div>
            <div class="item">
                <img alt="" src="{{ mExists('css/fimages/banner2.png') }}"/>
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
    {{-- 导航示例 开始 --}}
    @php
        $nav_menu = App\Model\Navigation::findData('test');
    @endphp
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                @if(isset($nav_menu[0]))
                    <a class="navbar-brand"
                       href="{{ $nav_menu[0]['nav_link'] }}"><b>{{ $nav_menu[0]['nav_text'] }}</b></a>
                @endif
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @if(isset($nav_menu))
                        @foreach ($nav_menu as $key => $data)
                            @if (0 < $key)
                                <li class="@if ($data['nav_active'])active @endif">
                                    @if ($data['nav_child'])
                                        <a data-toggle="dropdown" href="#"><b>{{ $data['nav_text'] }}</b><span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu" role="menu">
                                            @foreach ($data['nav_child'] as $child_data)
                                                <li><a href="{{ $child_data['nav_link'] }}"
                                                       target="{{ $child_data['nav_target'] }}"><b>{{ $child_data['nav_text'] }}</b></a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <a href="{{ $data['nav_url'] }}"
                                           target="{{ $data['nav_target'] }}"><b>{{ $data['nav_text'] }}</b></a>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
                <form class="navbar-form navbar-right" role="search" action="{{ route('Home::Article::search') }}"
                      method="post">
                    {{ csrf_field() }}
                    @if (isset($category_position['id']))
                        <input type="hidden" name="cate_id" value="{{ $category_position['id'] }}">
                    @endif
                    <div class="form-group">
                        <select name="type" class="form-control">
                            <option value="title"
                                    @if (isset($request['type']) && $request['type'] == 'type')selected="selected"@endif >@lang('common.search')@lang('common.type')</option>
                            <option value="description"
                                    @if (isset($request['type']) && $request['type'] == 'description')selected="selected"@endif>@lang('common.description')</option>
                            <option value="content"
                                    @if (isset($request['type']) && $request['type'] == 'content')selected="selected"@endif>@lang('common.content')</option>
                            @if (isset($category_position['extend']))
                                @foreach ($category_position['extend'] as $extend)
                                    <option value="extend[{{ $extend }}]"
                                            @if (isset($request['type']) && $request['type'] == 'extend['.$extend.']')selected="selected"@endif>
                                        {{ str_replace('common.','',trans('common.'.$extend)) }}
                                    </option>
                                @endforeach
                            @endif
                            <option value="all">@lang('common.all')</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input name="keyword" value="@if (isset($request['type'])){{ $request['keyword'] }}@endif"
                               type="text" class="form-control"
                               placeholder="@lang('common.keywords')">
                    </div>
                    <button type="submit" class="btn btn-default"><b>@lang('common.search')</b></button>
                </form>
            </div>
        </div>
    </nav>
    {{-- 导航示例 结束 --}}
</header>