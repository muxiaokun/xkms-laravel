@extends('Article:base')
@section('content')
    <div class="col-sm-12 text-center">
        <h2>{{ $category_info['name'] }}</h2>
    </div>
    <div class="col-sm-12 text-center">
        <button id="big_obj" class="btn btn-sm btn-default">@lang('common.tobig')@lang('common.font')</button>
        <button id="small_obj" class="btn btn-sm btn-default">@lang('common.tosmall')@lang('common.font')</button>
    </div>
    <div id="content" class="col-sm-12 mt20">
        {{ $category_info['content'] }}
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
@endsection