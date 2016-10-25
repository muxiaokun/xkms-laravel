@extends('admin.layout')
@section('body')
    <section class="container mt10">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">
                @include('admin.public_whereInfo')
                <table class="table table-condensed table-hover">
                    <tr>
                        <th><input type="checkbox" onClick="M_allselect_par(this,'table')"/>&nbsp;@lang('common.id')
                        </th>
                        <th>@lang('common.title')</th>
                        <th>@lang('common.assess')@lang('common.group')</th>
                        <th>@lang('common.start')@lang('common.time')</th>
                        <th>@lang('common.end')@lang('common.time')</th>
                        <th>@lang('common.yes')@lang('common.no')@lang('common.enable')</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('add') }}">@lang('common.add')@lang('common.assess')</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($assess_list as $assess)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $assess['id'] }}"/>
                                &nbsp;{{ $assess['id'] }}
                            </td>
                            <td>
                                {{ $assess['title'] }}
                            </td>
                            <td>
                                {{ $assess['group_name'] }}
                            </td>
                            <td>
                                {{ mDate($assess['start_time']) }}
                            </td>
                            <td>
                                {{ mDate($assess['end_time']) }}
                            </td>
                            <td>
                                @if ($assess['is_enable'])@lang('common.enable')@else@lang('common.disable')@endif
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['log_edit'])
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('Admin::AssessLog::edit',array('id'=>$assess['id'])) }}">
                                        @lang('common.statistics')
                                    </a>
                                @endif
                                @if ($batch_handle['log_edit'] AND $batch_handle['edit'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('edit',array('id'=>$assess['id'])) }}">
                                        @lang('common.edit')
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $assess['title'] }}?','{{ route('del',array('id'=>$assess['id'])) }}')">
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
                                        'name': lang.commonenable,
                                        'post_link': '{{ route('edit') }}',
                                        'post_data': {'is_enable': '1'}
                                    });
                                    config.type_data.push({
                                        'name': lang.commondisable,
                                        'post_link': '{{ route('edit') }}',
                                        'post_data': {'is_enable': '0'}
                                    });
                                    @endif
                                    @if ($batch_handle['del'])
                                        config.type_data.push({
                                        'name': lang.commondel,
                                        'post_link': '{{ route('del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        <M:Page name="assess_list">
                            <config></config>
                        </M:Page>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection