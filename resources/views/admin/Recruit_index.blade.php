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
                        <th>@lang('recruit.recruit')@lang('common.title')</th>
                        <th>@lang('common.yes')@lang('common.no')@lang('common.enable')</th>
                        <th>@lang('recruit.re_recruit')@lang('common.number')</th>
                        <th>@lang('common.start')@lang('common.time')</th>
                        <th>@lang('common.end')@lang('common.time')</th>
                        <td class="nowrap">
                            @if ($batch_handle['add'])
                                <a class="btn btn-xs btn-success"
                                   href="{{ route('Admin::Recruit::add') }}">@lang('common.add')@lang('recruit.recruit')</a>
                            @endif
                        </td>
                    </tr>
                    @foreach ($recruit_list as $recruit)
                        <tr>
                            <td>
                                <input name="id[]" type="checkbox" value="{{ $recruit['id'] }}"/>
                                &nbsp;{{ $recruit['id'] }}
                            </td>
                            <td>
                                {{ $recruit['title'] }}
                            </td>
                            <td>
                                @if ($recruit['is_enable'])@lang('common.enable')@else @lang('common.disable')@endif
                            </td>
                            <td>
                                {{ $recruit['current_portion'] }}/{{ $recruit['max_portion'] }}
                            </td>
                            <td>
                                {{ $recruit['start_time'] }}
                            </td>
                            <td>
                                {{ $recruit['end_time'] }}
                            </td>
                            <td class="nowrap">
                                @if ($batch_handle['log_index'])
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('Admin::RecruitLog::index',array('r_id'=>$recruit['id'])) }}">
                                        @lang('common.look')@lang('recruit.re_recruit')
                                    </a>
                                @endif
                                @if ($batch_handle['log_index'] AND $batch_handle['edit'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['edit'])
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('Admin::Recruit::edit',array('id'=>$recruit['id'])) }}">
                                        @lang('common.edit')
                                    </a>
                                @endif
                                @if ($batch_handle['edit'] AND $batch_handle['del'])&nbsp;|&nbsp;@endif
                                @if ($batch_handle['del'])
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0);"
                                       onClick="return M_confirm('@lang('common.confirm')@lang('common.del'){{ $recruit['title'] }}?','{{ route('Admin::Recruit::del',array('id'=>$recruit['id'])) }}')">
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
                                        'name': lang.common.enable,
                                        'post_link': '{{ route('Admin::Recruit::edit') }}',
                                        'post_data': {'is_enable': '1'}
                                    });
                                    config.type_data.push({
                                        'name': lang.common.disable,
                                        'post_link': '{{ route('Admin::Recruit::edit') }}',
                                        'post_data': {'is_enable': '0'}
                                    });
                                    @endif
                                    @if ($batch_handle['del'])
                                        config.type_data.push({
                                        'name': lang.common.del,
                                        'post_link': '{{ route('Admin::Recruit::del') }}'
                                    });
                                    @endif
                                            new M_batch_handle(config);
                                });
                            </script>
                        @endif
                    </div>
                    <div class="col-sm-8 text-right">
                        {{ $recruit_list->links('admin.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection