<script type="text/javascript" src="{{ asset('js/M_cate_tree.js') }}"></script>
<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            @include('Public:where_info')
            <table class="table table-condensed table-hover">
                <tr>
                    <th></th>
                    <th class="col-sm-1">@lang('common.sort')</th>
                    <th class="col-sm-1">@lang('common.yes')@lang('common.no')@lang('common.show')</th>
                    <td class="col-sm-3  nowrap">
                        @if ($batch_handle['add'])
                            <a class="btn btn-xs btn-success"
                               href="{{ route('add') }}">@lang('common.add')@lang('common.category')</a>
                        @endif
                    </td>
                </tr>
                @foreach ($article_category_list as $article_category)
                    <tr cate_id="{{ $article_category['id'] }}" parent_id="{{ $article_category['parent_id'] }}"
                        has_child="{{ $article_category['has_child'] }}">
                        <td>
                            <span class="glyphicon @if (0 < $article_category['has_child'])glyphicon-plus@elseglyphicon-minus@endif mlr10"
                                  onclick="M_cate_tree(this,article_category_cb);"></span>
                            {{ $article_category['name'] }}(ID:{{ $article_category['id'] }})
                        </td>
                        <td onClick="M_line_edit(this);" field_id="{{ $article_category['id'] }}" field="sort"
                            link="{{ route('ajax_api') }}">
                            {{ $article_category['sort'] }}
                        </td>
                        <td>
                            {{ $article_category['show'] }}
                        </td>
                        <td class="nowrap">
                            <a class="btn btn-xs btn-primary" target="_blank"
                               href="{{ route('Home/Article/category',array('cate_id'=>$article_category['id'])) }}">
                                @lang('common.look')
                            </a>
                            @if ($batch_handle['edit'])
                                &nbsp;|&nbsp;
                                <a class="btn btn-xs btn-primary"
                                   href="{{ route('edit',array('id'=>$article_category['id'])) }}">
                                    @lang('common.edit')
                                </a>
                            @endif
                            @if ($batch_handle['del'])
                                &nbsp;|&nbsp;
                                <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                   onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $article_category['name'] }}?','{{ route('del',array('id'=>$article_category['id'])) }}')">
                                    @lang('common.del')
                                </a>
                            @endif
                            &nbsp;|&nbsp;
                            <a class="btn btn-xs btn-primary"
                               href="{{ route('Article/add',array('cate_id'=>$article_category['id'])) }}">
                                @lang('common.add')@lang('common.article')
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</section>