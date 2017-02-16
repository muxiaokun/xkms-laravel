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
                        <th>@lang('common.title')</th>
                        <th>@lang('common.sort')</th>
                        <th>@lang('common.channel')</th>
                        <th>@lang('common.category')</th>
                        <th>@lang('common.add')@lang('common.time')</th>
                        <th>@lang('common.show')</th>
                        <th>@lang('common.audit')</th>
                        <th>@lang('common.click')@lang('common.number')</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('Admin::Article::add') }}">@lang('common.add')@lang('common.article')</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($article_list as $article)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $article['id'] }}"/>
                                &nbsp;{{ $article['id'] }}
                            </td>
                            <td>
                                {{ $article['title'] }}
                            </td>
                            <td onClick="M_line_edit(this);" field_id="{{ $article['id'] }}" field="sort"
                                link="{{ route('Admin::Article::ajax_api') }}">
                                {{ $article['sort'] }}
                            </td>
                            <td>
                                {{ $article['channel_name'] }}
                            </td>
                            <td>
                                {{ $article['cate_name'] }}
                            </td>
                            <td>
                                {{ $article['created_at'] }}
                            </td>
                            <td>
                                @if ($article['if_show'])@lang('common.yes')@else @lang('common.no')@endif
                            </td>
                            <td>
                                @if ($article['is_audit'])@lang('common.yes')@else @lang('common.no')@endif
                            </td>
                            <td>
                                {{ $article['hits'] }}
                            </td>
                            <td class="nowrap">
                                <a class="btn btn-xs btn-primary" target="_blank"
                                   href="{{ route('Home::Article::article',array('id'=>$article['id'])) }}">
                                    @lang('common.look')
                                </a>
                                @if ($batch_handle['edit'])
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('Admin::Article::edit',array('id'=>$article['id'])) }}">
                                        @lang('common.edit')
                                    </a>
                                @endif
                                @if ($batch_handle['del'])
                                    &nbsp;|&nbsp;
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $article['title'] }}?','{{ route('Admin::Article::del',array('id'=>$article['id'])) }}')">
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
                                        'name': lang.common.show,
                                        'post_link': '{{ route('Admin::Article::edit') }}',
                                        'post_data': {'if_show': '1'}
                                    });
                                    config.type_data.push({
                                        'name': lang.common.hidden,
                                        'post_link': '{{ route('Admin::Article::edit') }}',
                                        'post_data': {'if_show': '0'}
                                    });
                                    config.type_data.push({
                                        'name': lang.common.audit,
                                        'post_link': '{{ route('Admin::Article::edit') }}',
                                        'post_data': {'is_audit': '1'}
                                    });
                                    config.type_data.push({
                                        'name': lang.common.cancel + lang.common.audit,
                                        'post_link': '{{ route('Admin::Article::edit') }}',
                                        'post_data': {'is_audit': '0'}
                                    });
                                    @endif
                                    @if ($batch_handle['del'])
                                        config.type_data.push({
                                        'name': lang.common.del,
                                        'post_link': '{{ route('Admin::Article::del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="article_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection