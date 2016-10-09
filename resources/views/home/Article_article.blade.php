@extends('Article:base')
@section('content')
    <div class="col-sm-12 text-center">
        <h2>{{ $article_info['title'] }}</h2>
    </div>
    <div class="col-sm-12 text-center">
        @if ($article_info['add_time'])
            @lang('common.add')@lang('common.time')@lang('common.colon'){{ $article_info['add_time']|M_date="Y-m-d" }}
            &nbsp;&nbsp;
        @endif
        @if ($article_info['update_time'])
            @lang('common.edit')@lang('common.time')@lang('common.colon'){{ $article_info['update_time']|M_date="Y-m-d" }}
            &nbsp;&nbsp;
        @endif
        @if ($article_info['hits'])
            @lang('common.click')@lang('common.colon'){{ $article_info['hits'] }}&nbsp;&nbsp;
        @endif
        @if ($article_info['author'])
            @lang('common.author')@lang('common.colon'){{ $article_info['author'] }}&nbsp;&nbsp;
        @endif
        &nbsp;&nbsp;
        <button id="big_obj" class="btn btn-sm btn-default">@lang('common.tobig')@lang('common.font')</button>
        <button id="small_obj" class="btn btn-sm btn-default">@lang('common.tosmall')@lang('common.font')</button>
    </div>
    <div id="content" class="col-sm-12 mt20">
        {{ $article_info['content'] }}
    </div>
    <script type="text/javascript" src="{{ asset('js/M_fontsize.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            var config = {
                'out_obj': $('#content'),
                'big_obj': $('#big_obj'),
                'small_obj': $('#small_obj')
            };
            new M_fontsize(config);
        });
    </script>
    <div class="col-sm-12 mt20">
        <div class="col-sm-6 ">
            @if ($article_pn['p'])
                @lang('common.before'){{ $article_pn['limit'] }}@lang('common.piece')@lang('common.article')
                @foreach ($article_pn['p'] as $data)
                    <a href="{:M_U('article',$data['id'])}">{{ $data['title'] }}</a>
                @endforeach
            @endif
        </div>
        <div class="col-sm-6 ">
            @if ($article_pn['n'])
                @lang('common.later'){{ $article_pn['limit'] }}@lang('common.piece')@lang('common.article')
                @foreach ($article_pn['n'] as $data)
                    <a href="{:M_U('article',$data['id'])}">{{ $data['title'] }}</a>
                @endforeach
            @endif
        </div>
    </div>
    @include('Comment/index" controller="{{ $Think['const']['CONTROLLER_NAME'] }}" item="{{ $article_info['id'] }}')
@endsection