<section class="container mt10">
    <div class="panel panel-default">
        <div class="panel-heading">{{ $title }}</div>
        <div class="panel-body">
            @include('Public:where_info')
            <table class="table table-condensed table-hover">
                <tr>
                    <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')</th>
                    <th>@lang('common.title')</th>
                    <th>@lang('common.portion')</th>
                    <th>@lang('common.start')@lang('common.time')</th>
                    <th>@lang('common.end')@lang('common.time')</th>
                    <th>@lang('common.access')@lang('common.pass')</th>
                    <td class="nowrap">
                        @if ($batch_handle['add'])
                            <a class="btn btn-xs btn-success"
                               href="{{ route('add') }}">@lang('common.add')@lang('common.quests')</a>
                        @endif
                    </td>
                </tr>
                @foreach ($quests_list as $quests)
                    <tr>
                        <td>
                            <input name="id[]" type="checkbox" value="{{ $quests['id'] }}"/>
                            &nbsp;{{ $quests['id'] }}
                        </td>
                        <td>
                            {{ $quests['title'] }}
                        </td>
                        <td>
                            {{ $quests['current_portion'] }}/{{ $quests['max_portion'] }}
                        </td>
                        <td>
                            {{ $quests['start_time']|M_date=C('SYS_DATE_DETAIL') }}
                        </td>
                        <td>
                            {{ $quests['end_time']|M_date=C('SYS_DATE_DETAIL') }}
                        </td>
                        <td>
                            {{ $quests['access_info'] }}
                        </td>
                        <td class="nowrap">
                            @if ($batch_handle['answer_index'])
                                <a class="btn btn-xs btn-primary"
                                   href="{{ route('QuestsAnswer/index',array('quests_id'=>$quests['id'])) }}">
                                    @lang('common.answer')@lang('common.list')
                                </a>
                            @endif
                            @if ($batch_handle['answer_index'] AND $batch_handle['answer_edit'])&nbsp;|&nbsp;@endif
                            @if ($batch_handle['answer_edit'])
                                <a class="btn btn-xs btn-primary"
                                   href="{{ route('QuestsAnswer/edit',array('quests_id'=>$quests['id'])) }}">
                                    @lang('common.statistics')@lang('common.quests')
                                </a>
                            @endif
                            @if ($batch_handle['answer_edit'] AND $batch_handle['edit'])&nbsp;|&nbsp;@endif
                            @if ($batch_handle['edit'])
                                <a class="btn btn-xs btn-primary" href="{{ route('edit',array('id'=>$quests['id'])) }}">
                                    @lang('common.edit')
                                </a>
                            @endif
                            @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                            @if ($batch_handle['del'])
                                <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                   onClick="return M_confirm('@lang('common.confirm')@lang('common.clear'){{ $quests['title'] }}@lang('common.answer')?',
                                           '{{ route('del',array('id'=>$quests['id'],'clear'=>1)) }}')">
                                    @lang('common.clear')@lang('common.answer')
                                </a>
                                &nbsp;|&nbsp;
                                <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                   onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $quests['title'] }}?','{{ route('del',array('id'=>$quests['id'])) }}')">
                                    @lang('common.del')
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            <div class="row">
                <div id="batch_handle" class="col-sm-4 pagination">
                    @if ($batch_handle['del'])
                        <script type="text/javascript" src="{{ asset('js/M_batch_handle.js') }}"></script>
                        <script type="text/javascript">
                            $(function () {
                                var config = {
                                    'out_obj': $('#batch_handle'),
                                    'post_obj': 'input[name="id"]',
                                    'type_data': Array()
                                };
                                @if ($batch_handle['del'])
                                    config.type_data.push({'name': $Think.lang.del, 'post_link': '{{ route('del') }}'});
                                @endif
                                        new M_batch_handle(config);
                            });
                        </script>
                    @endif
                </div>
                <div class="col-sm-8 text-right">
                    <M:Page name="quests_list">
                        <config></config>
                    </M:Page>
                </div>
            </div>
        </div>
    </div>
</section>
