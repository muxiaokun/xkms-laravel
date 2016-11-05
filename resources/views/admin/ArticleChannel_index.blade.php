@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('admin.Public_whereInfo')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')
                        </th>
                        <th>@lang('common.channel')@lang('common.name')</th>
                        <th>@lang('common.yes')@lang('common.no')@lang('common.show')</th>
                        <th>@lang('common.channel')@lang('common.template')</th>
                        <td class="col-sm-2 nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('Admin::ArticleChannel::add') }}">@lang('common.add')@lang('common.channel')</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($article_channel_list as $article_channel)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $article_channel['id'] }}"/>
                                &nbsp;{{ $article_channel['id'] }}
                            </td>
                            <td>
                                {{ $article_channel['name'] }}
                            </td>
                            <td>
                                @if ($article_channel['if_show'])@lang('common.show')@else@lang('common.hidden')@endif
                            </td>
                            <td>
                                @if ($article_channel['template']){{ $article_channel['template'] }}@else@lang('common.default')@endif
                            </td>
                            <td class="nowrap">
                                <a class="btn btn-xs btn-primary" target="_blank"
                                   href="{{ route('Home::Article::channel',array('channel_id'=>$article_channel['id'])) }}">
                                    @lang('common.look')
                                </a>
                                @if ($batch_handle['edit'])
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('Admin::ArticleChannel::edit',array('id'=>$article_channel['id'])) }}">
                                        @lang('common.edit')
                                    </a>
                                @endif
                                @if ($batch_handle['del'])
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $article_channel['name'] }}?','{{ route('Admin::ArticleChannel::del',array('id'=>$article_channel['id'])) }}')">
                                        @lang('common.del')
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="row">
                    <div id="batch_handle" class="col-sm-4 pagination">
                        @if ($batch_handle['edit'] OR $batch_handle['del'])
                            <script type="text/javascript" src="{{ asset('js/M_batch_handle.js') }}"></script>
                            <script type="text/javascript">
                                $(function () {
                                    var config = {
                                        'out_obj': $('#batch_handle'),
                                        'post_obj': 'input[name="id"]',
                                        'type_data': Array()
                                    };
                                    @if ($batch_handle['edit'])
                                        config.type_data.push({
                                        'name': lang.commonshow,
                                        'post_link': '{{ route('Admin::ArticleChannel::edit') }}',
                                        'post_data': {'if_show': '1'}
                                    });
                                    config.type_data.push({
                                        'name': lang.commonhidden,
                                        'post_link': '{{ route('Admin::ArticleChannel::edit') }}',
                                        'post_data': {'if_show': '0'}
                                    });
                                    @endif
                                    @if ($batch_handle['del'])
                                        config.type_data.push({
                                        'name': lang.commondel,
                                        'post_link': '{{ route('Admin::ArticleChannel::del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="article_channel_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection